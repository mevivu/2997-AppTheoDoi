<?php

namespace App\Api\V1\Http\Controllers\AppFeedback;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\AppFeedback\StoreAppFeedbackRequest;
use App\Api\V1\Repositories\AppVersion\AppVersionRepositoryInterface;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Enums\Feedback\AppFeedbackFeature;
use App\Enums\Feedback\AppFeedbackStatus;
use App\Enums\Package\PackageStatus;
use App\Enums\Package\PackageType;
use App\Models\AppFeedback;
use App\Models\Package;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * @group Form Đánh Giá Chăm Con 360
 */
class AppFeedbackController extends Controller
{
    use Response, UseLog;

    protected AppVersionRepositoryInterface $appVersionRepository;

    public function __construct(AppVersionRepositoryInterface $appVersionRepository)
    {
        $this->middleware('auth:api');
        $this->appVersionRepository = $appVersionRepository;
    }

    /**
     * Lấy thông tin cấu hình form đánh giá, trạng thái hiện tại của người dùng và link cửa hàng
     */
    public function myStatus(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $rewardPackage = $this->resolveRewardPackage();

            $androidVersion = $this->appVersionRepository->findActiveVersion('user', 'android');
            $iosVersion = $this->appVersionRepository->findActiveVersion('user', 'ios');

            $feedback = AppFeedback::with('grantedPackage')
                ->where('user_id', $user->id)
                ->latest('id')
                ->first();

            return $this->jsonResponseSuccess([
                'user_id' => $user->id,
                'email' => $user->decrypted_email,
                'questions' => [
                    'favorite_feature_options' => AppFeedbackFeature::options(),
                ],
                'reward_package' => $rewardPackage ? [
                    'id' => $rewardPackage->id,
                    'name' => $rewardPackage->name,
                    'price' => (float) $rewardPackage->price,
                    'days' => (int) $rewardPackage->days,
                ] : null,
                'store_links' => [
                    'google_play_url' => $androidVersion?->update_url,
                    'app_store_url' => $iosVersion?->update_url,
                ],
                'feedback' => $feedback ? $this->formatFeedback($feedback) : null,
            ]);
        } catch (Throwable $e) {
            $this->logError('Get app feedback status failed:', $e);
            return $this->jsonResponseError($e->getMessage(), 500);
        }
    }

    /**
     * Gửi đánh giá Chăm Con 360
     */
    public function store(StoreAppFeedbackRequest $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Kiểm tra nếu đã được cấp VIP từ chương trình này
            $alreadyGranted = AppFeedback::where('user_id', $user->id)
                ->where('status', AppFeedbackStatus::VIP_GRANTED->value)
                ->exists();

            if ($alreadyGranted) {
                return $this->jsonResponseError('Tài khoản của bạn đã nhận ưu đãi gói VIP từ chương trình đánh giá.', 400);
            }

            $existingPending = AppFeedback::where('user_id', $user->id)
                ->where('status', AppFeedbackStatus::PENDING->value)
                ->latest('id')
                ->first();

            if ($existingPending) {
                $existingPending->update([
                    'email' => $user->decrypted_email,
                    'rating' => (int) $request->input('rating'),
                    'favorite_feature' => $request->input('favorite_feature'),
                    'improvement_feedback' => trim($request->input('improvement_feedback')),
                ]);
                $feedback = $existingPending->fresh();
            } else {
                $feedback = AppFeedback::create([
                    'user_id' => $user->id,
                    'email' => $user->decrypted_email,
                    'rating' => (int) $request->input('rating'),
                    'favorite_feature' => $request->input('favorite_feature'),
                    'improvement_feedback' => trim($request->input('improvement_feedback')),
                    'status' => AppFeedbackStatus::PENDING->value,
                ]);
            }

            return $this->jsonResponseSuccess(
                $this->formatFeedback($feedback),
                __('Gửi đánh giá thành công! Cảm ơn bạn đã chia sẻ trải nghiệm cùng Chăm Con 360.')
            );
        } catch (Throwable $e) {
            $this->logError('Store app feedback failed:', $e);
            return $this->jsonResponseError($e->getMessage(), 500);
        }
    }

    protected function resolveRewardPackage(): ?Package
    {
        $configuredId = Setting::where('setting_key', AppFeedback::SETTING_REWARD_PACKAGE_ID)->value('plain_value');

        if ($configuredId) {
            $pkg = Package::where('status', PackageStatus::Active)->find($configuredId);
            if ($pkg) {
                return $pkg;
            }
        }

        return Package::where('status', PackageStatus::Active)
            ->where('type', PackageType::OneYear)
            ->where('is_sale', false)
            ->first()
            ?: Package::where('status', PackageStatus::Active)->orderByDesc('days')->first();
    }

    protected function formatFeedback(AppFeedback $feedback): array
    {
        return [
            'id' => $feedback->id,
            'user_id' => $feedback->user_id,
            'email' => $feedback->email,
            'rating' => (int) $feedback->rating,
            'favorite_feature' => $feedback->favorite_feature,
            'improvement_feedback' => $feedback->improvement_feedback,
            'status' => $feedback->status?->value ?? AppFeedbackStatus::PENDING->value,
            'status_label' => $feedback->status?->label() ?? __('Chờ duyệt'),
            'granted_package' => $feedback->grantedPackage ? [
                'id' => $feedback->grantedPackage->id,
                'name' => $feedback->grantedPackage->name,
                'price' => (float) $feedback->grantedPackage->price,
                'days' => (int) $feedback->grantedPackage->days,
            ] : null,
            'vip_granted_at' => $feedback->vip_granted_at?->toIso8601String(),
            'submitted_at' => $feedback->created_at?->toIso8601String(),
        ];
    }
}
