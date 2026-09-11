<?php

namespace App\Admin\Http\Controllers\User;

use App\Admin\DataTables\User\KycApprovalDatatable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\User\RejectKycRequest;
use App\Enums\Notification\MessageType;
use App\Enums\User\KycStatus;
use App\Models\User;
use App\Traits\NotifiesViaFirebase;
use App\Traits\ResponseController;
use App\Traits\RouteAdminSystem;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class KycApprovalController extends Controller
{
    use ResponseController, NotifiesViaFirebase;

    public function __construct()
    {
        parent::__construct();
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.kyc.index',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => RouteAdminSystem::KYC_INDEX,
        ];
    }

    /**
     * Danh sách hồ sơ xác minh CCCD & MST của đối tác Affiliate
     */
    public function index(KycApprovalDatatable $dataTable)
    {
        // Thống kê nhanh theo các trạng thái KYC
        $baseQuery = User::where(function ($q) {
            $q->where('kyc_status', '!=', KycStatus::NOT_SUBMITTED->value)
                ->orWhereNotNull('id_card_front')
                ->orWhereNotNull('id_card_back')
                ->orWhereNotNull('tax_code');
        });

        $totalKyc = (clone $baseQuery)->count();
        $pendingCount = (clone $baseQuery)->where('kyc_status', KycStatus::PENDING->value)->count();
        $approvedCount = (clone $baseQuery)->where('kyc_status', KycStatus::APPROVED->value)->count();
        $rejectedCount = (clone $baseQuery)->where('kyc_status', KycStatus::REJECTED->value)->count();

        return $dataTable->render(
            $this->view['index'],
            [
                'breadcrumbs' => $this->crums->add(__('Duyệt CCCD & MST')),
                'totalKyc' => $totalKyc,
                'pendingCount' => $pendingCount,
                'approvedCount' => $approvedCount,
                'rejectedCount' => $rejectedCount,
                'currentStatus' => request('kyc_status') ?: request('status') ?: 'all',
            ]
        );
    }

    /**
     * Admin Phê duyệt hồ sơ CCCD & MST cho đối tác
     */
    public function approve(Request $request, int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            $user->update([
                'kyc_status' => KycStatus::APPROVED->value,
                'kyc_verified_at' => now(),
                'kyc_rejection_reason' => null,
                'kyc_rejected_at' => null,
            ]);

            // Gửi thông báo đẩy Firebase đến mobile app của đối tác
            $this->notifyUserKycApproved($user);

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => __('Đã phê duyệt xác minh CCCD thành công cho đối tác :name!', [
                    'name' => $user->fullname ?? '#' . $user->id,
                ]),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => $e->getMessage() ?: __('Phê duyệt thất bại. Vui lòng thử lại.'),
            ], 400);
        }
    }

    /**
     * Admin Từ chối hồ sơ CCCD & MST kèm lý do
     */
    public function reject(RejectKycRequest $request, int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $reason = trim($request->input('reason'));

            $user->update([
                'kyc_status' => KycStatus::REJECTED->value,
                'kyc_rejected_at' => now(),
                'kyc_rejection_reason' => $reason,
                'kyc_verified_at' => null,
            ]);

            // Gửi thông báo đẩy Firebase kèm lý do đến mobile app của đối tác
            $this->notifyUserKycRejected($user, $reason);

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => __('Đã từ chối hồ sơ CCCD của đối tác :name!', [
                    'name' => $user->fullname ?? '#' . $user->id,
                ]),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => $e->getMessage() ?: __('Từ chối hồ sơ thất bại. Vui lòng thử lại.'),
            ], 400);
        }
    }

    /**
     * Gửi Firebase Notification thông báo duyệt CCCD thành công
     */
    protected function notifyUserKycApproved(User $user): void
    {
        try {
            $title = 'Xác minh danh tính thành công';
            $body = 'Hồ sơ CCCD và Mã số thuế của bạn đã được Admin phê duyệt thành công. Bạn đã có thể rút tiền hoa hồng về tài khoản ngân hàng!';

            $deviceTokens = $user->devices()->whereNotNull('device_token')->pluck('device_token')->toArray();
            if (!empty($deviceTokens)) {
                $this->sendFirebaseNotification(
                    $deviceTokens,
                    null,
                    $title,
                    $body,
                    null,
                    [
                        'type' => 'kyc_approved',
                        'screen' => '/withdraw',
                        'kyc_status' => KycStatus::APPROVED->value,
                    ]
                );
            }
        } catch (Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Lỗi gửi FCM duyệt KYC: ' . $e->getMessage());
        }
    }

    /**
     * Gửi Firebase Notification thông báo từ chối CCCD kèm lý do
     */
    protected function notifyUserKycRejected(User $user, string $reason): void
    {
        try {
            $title = 'Xác minh danh tính không thành công';
            $body = "Hồ sơ CCCD của bạn bị từ chối với lý do: {$reason}. Vui lòng cập nhật lại thông tin.";

            $deviceTokens = $user->devices()->whereNotNull('device_token')->pluck('device_token')->toArray();
            if (!empty($deviceTokens)) {
                $this->sendFirebaseNotification(
                    $deviceTokens,
                    null,
                    $title,
                    $body,
                    null,
                    [
                        'type' => 'kyc_rejected',
                        'screen' => '/kyc-verification',
                        'reason' => $reason,
                        'kyc_status' => KycStatus::REJECTED->value,
                    ]
                );
            }
        } catch (Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Lỗi gửi FCM từ chối KYC: ' . $e->getMessage());
        }
    }
}
