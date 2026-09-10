<?php

namespace App\Api\V1\Services\Notification;

use App\Models\User;
use Illuminate\Http\Request;

interface NotificationServiceInterface
{

    public function getNotificationByUser(Request $request): bool|object;

    public function updateStatusIsRead(Request $request): bool;

    public function updateAllStatusIsRead(): bool;

    public function delete($id);

    public function sendNotificationsToAdmins(string $title, string $body, $type, bool $sendEmail = true);

    public function sendCustomerPaymentNotification(User $user): void;

    public function sendNotificationsPaymentToAdmins($user, $image, $packageId);
    public function sendPaymentSuccessNotification(User $user, string $packageName): void;

    /**
     * Gửi thông báo hoa hồng giới thiệu (In-app & Push FCM) đến người giới thiệu
     *
     * @param User $referrer Người giới thiệu nhận hoa hồng
     * @param float $amount Số tiền hoa hồng nhận được (VNĐ)
     * @param string $newUserName Tên người dùng mới đăng ký
     * @return void
     */
    public function sendAffiliateRewardNotification(User $referrer, float $amount, string $newUserName): void;

    /**
     * Gửi thông báo chào mừng thành viên mới (In-app notification, không gửi FCM)
     *
     * @param User $user Người dùng vừa đăng ký
     * @return void
     */
    public function sendWelcomeNotification(User $user): void;

    /**
     * Gửi thông báo chúc mừng thăng cấp bậc mẹ giới thiệu (In-app & Push FCM)
     *
     * @param User $referrer Người giới thiệu được thăng cấp
     * @param string $newRankName Tên cấp bậc mới
     * @param float $totalSales Tổng doanh số tích lũy hiện tại (VNĐ)
     * @return void
     */
    public function sendAffiliateRankUpgradeNotification(User $referrer, string $newRankName, float $totalSales): void;
}
