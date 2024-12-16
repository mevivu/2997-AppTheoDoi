<?php

namespace App\Api\V1\Repositories\Notification;


use App\Admin\Repositories\EloquentRepositoryInterface;

interface NotificationRepositoryInterface extends EloquentRepositoryInterface
{

    public function getNotificationByUserId($role, $userId, $limit = 10, $page = 1);

    public function getNotificationIsNotRead($userId);
}
