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
}
