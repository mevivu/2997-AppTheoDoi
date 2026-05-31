<?php

namespace App\Admin\Services\Notification;

use App\Admin\Repositories\Notification\NotificationRepositoryInterface;
use App\Admin\Traits\AuthService;
use App\Admin\Traits\Roles;
use App\Enums\Notification\MessageType;
use App\Models\User;
use App\Traits\NotifiesViaFirebase;

class NotificationFirebaseService implements NotificationFirebaseServiceInterface
{
    use NotifiesViaFirebase, AuthService, Roles;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected NotificationRepositoryInterface $repository;

    public function __construct(
        NotificationRepositoryInterface $repository,

    )
    {
        $this->repository = $repository;
    }


    public function notifyUserPackageApproved(User $user): void
    {
        $title = config('notifications.package_approved_and_paid.title');
        $bodyTemplate = config('notifications.package_approved_and_paid.message');
        $this->sendFirebaseNotificationToUser($user, $title, $bodyTemplate, MessageType::UNCLASSIFIED);

    }

    public function notifyUserLocked(User $user): void
    {
        $title = config('notifications.user_locked.title', 'Tài khoản đã bị khóa');
        $bodyTemplate = config('notifications.user_locked.message', 'Tài khoản của bạn đã bị khóa.');
        $this->sendFirebaseNotificationToUser($user, $title, $bodyTemplate, MessageType::LOCK);
    }

    public function notifyLoginAnotherDevice(User $user, string $oldDeviceToken): void
    {
        $title = config('notifications.login_another_device.title', 'Tài khoản đăng nhập trên thiết bị khác');
        $bodyTemplate = config('notifications.login_another_device.message', 'Tài khoản của bạn đã được đăng nhập từ một thiết bị mới. Phiên đăng nhập trên thiết bị này đã hết hạn.');

        $notification = $this->repository->create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $bodyTemplate,
            'type' => MessageType::LOGIN_ANOTHER_DEVICE->value,
            'is_pushed' => true
        ]);

        if (!empty($oldDeviceToken)) {
            $this->sendFirebaseNotification([$oldDeviceToken], null, $title, $bodyTemplate, $notification->id, [
                'type' => MessageType::LOGIN_ANOTHER_DEVICE->value
            ]);
        }
    }
}
