<?php

namespace App\Admin\Services\Notification;

use App\Models\User;
use App\Enums\Notification\MessageType;
interface NotificationFirebaseServiceInterface
{

    public function sendFirebaseNotificationToUser(User $user, string $title, string $body, ?MessageType $type = null);

    public function notifyUserPackageApproved(User $user);

    public function notifyUserLocked(User $user);
}
