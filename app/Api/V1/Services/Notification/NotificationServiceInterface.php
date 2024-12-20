<?php

namespace App\Api\V1\Services\Notification;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

interface NotificationServiceInterface
{

    public function getNotificationByUser(Request $request): bool|object;

    public function updateStatusIsRead(Request $request): bool;

    public function updateAllStatusIsRead(Request $request): bool;

    public function delete(Request $request): bool;

    public function sendApprovalNotificationToAdmin(User $user): void;
    public function sendNotificationsToAdmins(string $title, string $body, $type, bool $sendEmail = true);

    public function sendCustomerPaymentNotification(User $user): void;

    public function sendNotificationsPaymentToAdmins($user,$image,$packageId);


}
