<?php

namespace App\Admin\Http\Controllers\AppFeedback;

use App\Admin\DataTables\AppFeedback\AppFeedbackDatatable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\Notification\NotificationRepositoryInterface;
use App\Api\V1\Repositories\AppVersion\AppVersionRepositoryInterface;
use App\Enums\Feedback\AppFeedbackStatus;
use App\Enums\Notification\MessageType;
use App\Enums\Notification\NotificationStatus;
use App\Enums\Package\PackageStatus;
use App\Enums\Package\PackageType;
use App\Enums\Package\PackageUserStatus;
use App\Enums\Setting\SettingGroup;
use App\Enums\Setting\SettingTypeInput;
use App\Models\AppFeedback;
use App\Models\Package;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserPackage;
use App\Traits\NotifiesViaFirebase;
use App\Traits\ResponseController;
use App\Traits\RouteAdminSystem;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

class AppFeedbackController extends Controller
{
    use ResponseController, NotifiesViaFirebase;

    public function __construct()
    {
        parent::__construct();
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.app_feedback.index',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => RouteAdminSystem::APP_FEEDBACK_INDEX,
        ];
    }

    /**
     * Danh sách đánh giá Chăm Con 360 & Cấu hình gói VIP thưởng
     */
    public function index(AppFeedbackDatatable $dataTable, AppVersionRepositoryInterface $appVersionRepository)
    {
        $totalCount = AppFeedback::count();
        $pendingCount = AppFeedback::where('status', AppFeedbackStatus::PENDING->value)->count();
        $approvedCount = AppFeedback::where('status', AppFeedbackStatus::APPROVED->value)->count();
        $vipGrantedCount = AppFeedback::where('status', AppFeedbackStatus::VIP_GRANTED->value)->count();
        $rejectedCount = AppFeedback::where('status', AppFeedbackStatus::REJECTED->value)->count();

        // Danh sách các gói đang hoạt động để chọn trên CMS Admin
        $activePackages = Package::where('status', PackageStatus::Active)
            ->orderByDesc('days')
            ->orderBy('id')
            ->get();

        $configuredPackageId = Setting::where('setting_key', AppFeedback::SETTING_REWARD_PACKAGE_ID)->value('plain_value');
        $selectedRewardPackage = null;

        if ($configuredPackageId) {
            $selectedRewardPackage = $activePackages->firstWhere('id', (int) $configuredPackageId);
        }

        if (!$selectedRewardPackage) {
            $selectedRewardPackage = $activePackages->first(function (Package $pkg) {
                return $pkg->type === PackageType::OneYear && !$pkg->is_sale;
            }) ?: $activePackages->first();
        }

        // Lấy link cửa hàng từ Quản lý phiên bản
        $androidVersion = $appVersionRepository->findActiveVersion('user', 'android');
        $iosVersion = $appVersionRepository->findActiveVersion('user', 'ios');

        return $dataTable->render(
            $this->view['index'],
            [
                'breadcrumbs' => $this->crums->add(__('Form Đánh Giá Chăm Con 360')),
                'totalCount' => $totalCount,
                'pendingCount' => $pendingCount,
                'approvedCount' => $approvedCount,
                'vipGrantedCount' => $vipGrantedCount,
                'rejectedCount' => $rejectedCount,
                'currentStatus' => request('feedback_status') ?: request('status') ?: 'all',
                'activePackages' => $activePackages,
                'selectedRewardPackage' => $selectedRewardPackage,
                'googlePlayUrl' => $androidVersion?->update_url,
                'appStoreUrl' => $iosVersion?->update_url,
            ]
        );
    }

    /**
     * Lưu cấu hình gói VIP thưởng mặc định từ danh sách gói hoạt động trên CMS Admin
     */
    public function updateRewardPackage(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'package_id' => ['required', 'integer'],
            ]);

            $package = Package::where('status', PackageStatus::Active)
                ->findOrFail((int) $request->input('package_id'));

            Setting::updateOrCreate(
                ['setting_key' => AppFeedback::SETTING_REWARD_PACKAGE_ID],
                [
                    'setting_name' => 'Gói VIP thưởng chương trình Đánh giá Chăm Con 360',
                    'plain_value' => (string) $package->id,
                    'type_input' => SettingTypeInput::Number,
                    'group' => SettingGroup::General,
                ]
            );

            Cache::forget(Setting::CACHE_KEY_GET_ALL);

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => __('Đã cập nhật gói thưởng chương trình: :name (:priceđ - :days ngày)', [
                    'name' => $package->name,
                    'price' => number_format((float) $package->price, 0, ',', '.'),
                    'days' => (int) $package->days,
                ]),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => $e->getMessage() ?: __('Không thể lưu cấu hình gói thưởng.'),
            ], 400);
        }
    }

    /**
     * Duyệt & Cấp gói VIP (chọn từ danh sách gói đang hoạt động) cho đúng User ID
     */
    public function grantVip(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'package_id' => ['required', 'integer'],
            ]);

            $package = Package::where('status', PackageStatus::Active)
                ->findOrFail((int) $request->input('package_id'));

            $result = DB::transaction(function () use ($id, $package) {
                /** @var AppFeedback $feedback */
                $feedback = AppFeedback::where('id', $id)->lockForUpdate()->firstOrFail();

                // Kiểm tra chống cấp trùng VIP cho cùng User ID
                $alreadyGranted = AppFeedback::where('user_id', $feedback->user_id)
                    ->where(function ($q) {
                        $q->where('status', AppFeedbackStatus::VIP_GRANTED->value)
                            ->orWhereNotNull('vip_granted_at');
                    })
                    ->lockForUpdate()
                    ->exists();

                if ($alreadyGranted) {
                    throw new Exception(__('Tài khoản User ID #:id đã được cấp gói VIP từ chương trình đánh giá trước đó. Hệ thống chặn cấp trùng!', [
                        'id' => $feedback->user_id,
                    ]));
                }

                $user = User::findOrFail($feedback->user_id);
                $daysToAdd = max(1, (int) ($package->days ?: 365));

                // Gia hạn / Kích hoạt gói trong bảng user_packages cho đúng User ID
                $currentUserPackage = UserPackage::where('user_id', $user->id)
                    ->lockForUpdate()
                    ->first();

                $remainingDays = 0;
                $now = Carbon::now();

                if (
                    $currentUserPackage
                    && $currentUserPackage->status === PackageUserStatus::Active
                    && $currentUserPackage->end_date
                    && $now->lt(Carbon::parse($currentUserPackage->end_date))
                ) {
                    $remainingDays = (int) $now->diffInDays(Carbon::parse($currentUserPackage->end_date));
                }

                $totalDays = $remainingDays + $daysToAdd;
                $startDate = $now;
                $endDate = $now->copy()->addDays($totalDays);

                if ($currentUserPackage) {
                    $currentUserPackage->update([
                        'package_id' => $package->id,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'status' => PackageUserStatus::Active,
                        'current_type' => $package->type,
                    ]);
                } else {
                    UserPackage::create([
                        'user_id' => $user->id,
                        'package_id' => $package->id,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'status' => PackageUserStatus::Active,
                        'current_type' => $package->type,
                    ]);
                }

                // Lưu lịch sử cấp thưởng
                $feedback->update([
                    'status' => AppFeedbackStatus::VIP_GRANTED->value,
                    'granted_package_id' => $package->id,
                    'vip_granted_at' => Carbon::now(),
                    'granted_by_admin_id' => auth('admin')->id(),
                ]);

                return [
                    'feedback' => $feedback,
                    'user' => $user,
                    'endDate' => $endDate,
                ];
            });

            // Gửi thông báo kết quả cho người dùng
            $this->notifyUserResult(
                $result['user'],
                $package,
                AppFeedbackStatus::VIP_GRANTED,
                $result['endDate']
            );

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => __('Đã duyệt và cấp gói ":pkg" thành công cho User ID #:uid!', [
                    'pkg' => $package->name,
                    'uid' => $result['user']->id,
                ]),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => $e->getMessage() ?: __('Cấp gói VIP thất bại. Vui lòng thử lại.'),
            ], 400);
        }
    }

    /**
     * Cập nhật trạng thái Đã duyệt (approved) hoặc Từ chối (rejected) và thông báo cho người dùng
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'status' => ['required', 'string', Rule::in([
                    AppFeedbackStatus::APPROVED->value,
                    AppFeedbackStatus::REJECTED->value,
                ])],
            ]);

            $newStatus = AppFeedbackStatus::from($request->input('status'));
            $feedback = AppFeedback::findOrFail($id);

            if ($feedback->status === AppFeedbackStatus::VIP_GRANTED) {
                return response()->json([
                    'status' => 400,
                    'success' => false,
                    'message' => __('Đánh giá này đã được cấp VIP, không thể đổi lại trạng thái.'),
                ], 400);
            }

            $feedback->update([
                'status' => $newStatus->value,
                'granted_by_admin_id' => auth('admin')->id(),
            ]);

            if ($feedback->user) {
                $this->notifyUserResult($feedback->user, null, $newStatus, null);
            }

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => __('Đã cập nhật trạng thái đánh giá thành ":status" cho User ID #:uid!', [
                    'status' => $newStatus->label(),
                    'uid' => $feedback->user_id,
                ]),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => $e->getMessage() ?: __('Cập nhật trạng thái thất bại.'),
            ], 400);
        }
    }

    /**
     * Lưu thông báo In-App và đẩy thông báo Firebase kết quả duyệt đánh giá cho người dùng
     */
    protected function notifyUserResult(
        User $user,
        ?Package $package,
        AppFeedbackStatus $status,
        ?Carbon $endDate
    ): void {
        try {
            if ($status === AppFeedbackStatus::VIP_GRANTED && $package) {
                $title = 'Chúc mừng! Bạn đã nhận được ưu đãi Gói VIP';
                $priceFormatted = number_format((float) $package->price, 0, ',', '.');
                $endFormatted = $endDate ? $endDate->format('d/m/Y') : '';
                $body = "Cảm ơn bạn đã chia sẻ trải nghiệm cùng Chăm Con 360. Tài khoản ID #{$user->id} đã được cấp {$package->name} (trị giá {$priceFormatted}đ), hiệu lực đến ngày {$endFormatted}.";
            } elseif ($status === AppFeedbackStatus::APPROVED) {
                $title = 'Đánh giá Chăm Con 360 đã được duyệt';
                $body = "Cảm ơn bạn đã gửi góp ý cho Chăm Con 360. Phản hồi của bạn đã được Admin ghi nhận và phê duyệt.";
            } else {
                $title = 'Thông báo kết quả đánh giá Chăm Con 360';
                $body = "Cảm ơn bạn đã gửi phản hồi cho Chăm Con 360. Đánh giá của bạn hiện chưa đủ điều kiện nhận ưu đãi theo thể lệ chương trình góp ý.";
            }

            $deviceTokens = collect([$user->device_token])
                ->merge($user->devices()->whereNotNull('device_token')->pluck('device_token'))
                ->filter()
                ->unique()
                ->values()
                ->all();

            $notificationRepository = app(NotificationRepositoryInterface::class);
            $notification = $notificationRepository->create([
                'user_id' => $user->id,
                'package_id' => $package?->id,
                'title' => $title,
                'message' => $body,
                'type' => MessageType::PAYMENT,
                'status' => NotificationStatus::NOT_READ,
                'is_pushed' => !empty($deviceTokens),
            ]);

            if (!empty($deviceTokens)) {
                $this->sendFirebaseNotification(
                    $deviceTokens,
                    null,
                    $title,
                    $body,
                    $notification->id,
                    [
                        'type' => 'app_feedback_result',
                        'feedback_status' => $status->value,
                    ]
                );
            }
        } catch (Throwable $e) {
            Log::error('Lỗi gửi thông báo kết quả đánh giá Chăm Con 360: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);
        }
    }
}
