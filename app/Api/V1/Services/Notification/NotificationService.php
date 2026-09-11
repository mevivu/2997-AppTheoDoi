<?php

namespace App\Api\V1\Services\Notification;


use App\Admin\Services\File\FileService;
use App\Admin\Traits\Roles;
use App\Api\V1\Repositories\Notification\NotificationRepositoryInterface;
use App\Api\V1\Repositories\User\UserRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Enums\Notification\MessageType;
use App\Enums\Notification\NotificationStatus;
use App\Models\User;
use App\Models\UserDevice;
use App\Traits\NotifiesViaFirebase;
use App\Api\V1\Support\UseLog;
use Illuminate\Http\Request;
use Exception;
use Throwable;


class NotificationService implements NotificationServiceInterface
{
    use NotifiesViaFirebase, Roles, UseLog;

    use AuthServiceApi;

    protected NotificationRepositoryInterface $repository;
    protected UserRepositoryInterface $userRepository;

    protected FileService $fileService;

    public function __construct(
        NotificationRepositoryInterface $repository,
        UserRepositoryInterface         $userRepository,
        FileService                     $fileService
    )
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->fileService = $fileService;


    }

    public function getNotificationByUser(Request $request): bool|object
    {
        try {
            $data = $request->validated();
            $userId = $this->getCurrentUserId();
            $limit = $data['limit'] ?? 10;
            $page = $data['page'] ?? 1;
            return $this->repository->getNotificationByUserId("user_id", $userId, $limit, $page);
        } catch (Exception $e) {
            $this->logError('Failed to process get user', $e);
            return false;
        }

    }


    public function updateStatusIsRead(Request $request): bool
    {
        try {

            $this->repository->update($request->id, ["status" => NotificationStatus::READ]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateAllStatusIsRead(): bool
    {
        try {
            $userId = $this->getCurrentUserId();
            $notifications = $this->repository->getBy(
                [
                    'user_id' => $userId,
                    'status' => NotificationStatus::NOT_READ
                ]
            );
            foreach ($notifications as $notification) {

                $notification->update(["status" => NotificationStatus::READ]);
            }
            return true;
        } catch (Exception $e) {

            return false;
        }
    }

    /**
     * @throws Exception
     */
    public function delete($id): void
    {
        $notification = $this->repository->findOrFail($id);
        if ($notification->payment_confirmation_image) {
            $this->fileService->deleteModelImages($notification, ['payment_confirmation_image']);
        }
        $notification->delete();
    }

    /**
     * @throws Exception
     */


    public function sendCustomerPaymentNotification(User $user): void
    {
        $title = config('notifications.package_purchase_pending.title');
        $bodyTemplate = config('notifications.package_purchase_pending.message');
        $body = str_replace('{fullname}', $user->fullname, $bodyTemplate);
        $this->sendFirebaseNotificationToUser($user, $title, $body, MessageType::UNCLASSIFIED);

    }


    public function sendPaymentSuccessNotification(User $user, string $packageName): void
    {
        $title = config('notifications.payment_success.title');
        $message = config('notifications.payment_success.message');
        $body = str_replace(
            ['{fullname}', '{package_name}'],
            [$user->fullname, $packageName],
            $message
        );
        $this->sendFirebaseNotificationToUser($user, $title, $body, MessageType::PAYMENT);

    }

    public function sendRefundNotification(User $user, string $packageName): void
    {
        $title = config('notifications.payment_refunded.title');
        $message = config('notifications.payment_refunded.message');

        $body = str_replace(
            ['{fullname}', '{package_name}'],
            [$user->fullname, $packageName],
            $message
        );
        $data = [
            'type' => 'logout',
        ];

        $this->sendFirebaseNotificationToUser($user, $title, $body, MessageType::PAYMENT, $data);
    }

    /**
     * Gửi thông báo hoa hồng giới thiệu (In-app & Push FCM) đến người giới thiệu
     *
     * @param User $referrer Người giới thiệu nhận hoa hồng
     * @param float $amount Số tiền hoa hồng nhận được (VNĐ)
     * @param string $newUserName Tên người dùng mới đăng ký
     * @return void
     */
    public function sendAffiliateRewardNotification(User $referrer, float $amount, string $newUserName): void
    {
        try {
            if ($amount > 0) {
                $formattedAmount = number_format($amount, 0, ',', '.') . 'đ';
                $titleTemplate = config('notifications.affiliate_reward_referrer.title');
                $bodyTemplate = config('notifications.affiliate_reward_referrer.message');

                $title = str_replace('{amount}', $formattedAmount, $titleTemplate);
                $body = str_replace(
                    ['{new_user_name}', '{amount}'],
                    [$newUserName, $formattedAmount],
                    $bodyTemplate
                );
            } else {
                $title = config('notifications.affiliate_new_referral.title');
                $bodyTemplate = config('notifications.affiliate_new_referral.message');
                $body = str_replace('{new_user_name}', $newUserName, $bodyTemplate);
            }

            // Thu thập toàn bộ device tokens của người giới thiệu
            $deviceTokens = collect([$referrer->device_token])
                ->merge(UserDevice::where('user_id', $referrer->id)->where('is_active', true)->pluck('device_token'))
                ->filter()
                ->unique()
                ->values()
                ->all();

            // 1. Tạo bản ghi thông báo trong ứng dụng (In-app notification) thông qua repository
            $notification = $this->repository->create([
                'user_id' => $referrer->id,
                'title' => $title,
                'message' => $body,
                'status' => NotificationStatus::NOT_READ,
                'type' => MessageType::AFFILIATE,
                'is_pushed' => !empty($deviceTokens),
            ]);

            // 2. Gửi Push Notification qua Firebase nếu tài khoản có device_token
            if (!empty($deviceTokens)) {
                $this->sendFirebaseNotification(
                    $deviceTokens,
                    null,
                    $title,
                    $body,
                    $notification->id,
                    [
                        'type' => 'affiliate',
                        'screen' => '/referral',
                        'amount' => (string) $amount,
                    ]
                );
            }
        } catch (Throwable $e) {
            $this->logError("Không thể gửi thông báo hoa hồng affiliate: " . $e->getMessage(), $e);
        }
    }

    /**
     * Gửi thông báo chào mừng thành viên mới khi đăng ký tài khoản (chỉ lưu in-app notification, không gửi FCM)
     *
     * @param User $user Người dùng vừa đăng ký
     * @return void
     */
    public function sendWelcomeNotification(User $user): void
    {
        try {
            $displayName = $user->fullname ?: 'Bạn';
            $title = config('notifications.welcome_user.title');
            $messageTemplate = config('notifications.welcome_user.message');

            $message = str_replace('{fullname}', $displayName, $messageTemplate);

            $this->repository->create([
                'user_id' => $user->id,
                'title' => $title,
                'message' => $message,
                'status' => NotificationStatus::NOT_READ,
                'type' => MessageType::UNCLASSIFIED,
                'is_pushed' => false,
            ]);
        } catch (Throwable $e) {
            $this->logError("Không thể tạo thông báo chào mừng thành viên mới: " . $e->getMessage(), $e);
        }
    }

    /**
     * Gửi thông báo chúc mừng thăng cấp bậc mẹ giới thiệu (In-app & Push FCM)
     *
     * @param User $referrer Người giới thiệu được thăng cấp
     * @param string $newRankName Tên cấp bậc mới (ví dụ: Bạc, Vàng, Bạch Kim, Kim Cương)
     * @param float $totalSales Tổng doanh số tích lũy hiện tại (VNĐ)
     * @return void
     */
    public function sendAffiliateRankUpgradeNotification(User $referrer, string $newRankName, float $totalSales): void
    {
        try {
            $displayName = $referrer->fullname ?: 'Mẹ';
            $formattedSales = number_format($totalSales, 0, ',', '.') . 'đ';

            $titleTemplate = config('notifications.affiliate_rank_upgrade.title', '🎉 Chúc mừng bạn đã thăng cấp {rank_name}!');
            $messageTemplate = config('notifications.affiliate_rank_upgrade.message', 'Xin chúc mừng {fullname}! Với tổng doanh số giới thiệu tích lũy đạt {total_sales}, bạn đã chính thức đạt danh hiệu {rank_name} của CHĂM CON 360 với nhiều quyền lợi ưu đãi hấp dẫn.');

            $replace = [
                '{fullname}' => $displayName,
                '{rank_name}' => $newRankName,
                '{total_sales}' => $formattedSales,
            ];

            $title = strtr($titleTemplate, $replace);
            $body = strtr($messageTemplate, $replace);

            $deviceTokens = collect([$referrer->device_token])
                ->merge(UserDevice::where('user_id', $referrer->id)->where('is_active', true)->pluck('device_token'))
                ->filter()
                ->unique()
                ->values()
                ->all();

            $notification = $this->repository->create([
                'user_id' => $referrer->id,
                'title' => $title,
                'message' => $body,
                'status' => NotificationStatus::NOT_READ,
                'type' => MessageType::AFFILIATE,
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
                        'type' => 'affiliate_rank_upgrade',
                        'screen' => '/referral',
                        'new_rank' => $newRankName,
                        'total_sales' => (string) $totalSales,
                    ]
                );
            }
        } catch (Throwable $e) {
            $this->logError("Không thể gửi thông báo thăng cấp bậc affiliate: " . $e->getMessage(), $e);
        }
    }

    /**
     * Gửi thông báo nhận hoa hồng khi F1 mua gói dịch vụ (In-app & Push FCM)
     *
     * @param User $referrer Người giới thiệu nhận hoa hồng
     * @param float $commissionAmount Số tiền hoa hồng nhận được (VNĐ)
     * @param float $percent Tỷ lệ % hoa hồng theo cấp bậc
     * @param string $f1Name Tên người dùng F1 mua gói
     * @param string $packageName Tên gói dịch vụ
     * @return void
     */
    public function sendAffiliatePackageCommissionNotification(User $referrer, float $commissionAmount, float $percent, string $f1Name, string $packageName): void
    {
        try {
            $formattedAmount = number_format($commissionAmount, 0, ',', '.') . 'đ';

            $titleTemplate = config('notifications.affiliate_package_commission.title', '💰 Bạn nhận được {amount} hoa hồng mua gói!');
            $messageTemplate = config('notifications.affiliate_package_commission.message', 'Chúc mừng bạn! Thành viên {f1_name} vừa thanh toán thành công gói "{package_name}". Bạn được cộng {amount} ({percent}%) vào ví hoa hồng.');

            $replace = [
                '{amount}' => $formattedAmount,
                '{f1_name}' => $f1Name,
                '{package_name}' => $packageName,
                '{percent}' => (string) $percent,
            ];

            $title = strtr($titleTemplate, $replace);
            $body = strtr($messageTemplate, $replace);

            $deviceTokens = collect([$referrer->device_token])
                ->merge(UserDevice::where('user_id', $referrer->id)->where('is_active', true)->pluck('device_token'))
                ->filter()
                ->unique()
                ->values()
                ->all();

            $notification = $this->repository->create([
                'user_id' => $referrer->id,
                'title' => $title,
                'message' => $body,
                'status' => NotificationStatus::NOT_READ,
                'type' => MessageType::AFFILIATE,
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
                        'type' => 'affiliate_package_commission',
                        'screen' => '/referral',
                        'amount' => (string) $commissionAmount,
                        'percent' => (string) $percent,
                    ]
                );
            }
        } catch (Throwable $e) {
            $this->logError("Không thể gửi thông báo hoa hồng mua gói affiliate: " . $e->getMessage(), $e);
        }
    }
}


