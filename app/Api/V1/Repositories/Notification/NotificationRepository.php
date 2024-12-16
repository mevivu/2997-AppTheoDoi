<?php

namespace App\Api\V1\Repositories\Notification;

use \App\Admin\Repositories\Notification\NotificationRepository as AdminArea;

use App\Enums\Notification\NotificationStatus;
use App\Models\Notification;

class NotificationRepository extends AdminArea implements NotificationRepositoryInterface
{
    protected $model;

    public function __construct(Notification $note)
    {
        $this->model = $note;
    }

    public function getNotificationByUserId($role, $userId, $limit = 10, $page = 1)
    {
        if ($userId) {
            return $this->model->where($role, $userId)
                ->orderBy('id', 'desc')
                ->paginate($limit, ['*'], 'page', $page);
        }

        return false;
    }

    public function getNotificationIsNotRead($userId)
    {
        // TODO: Implement GetNotificationIsNotRead() method.
        return $this->model->where('user_id', $userId)->where('status', NotificationStatus::NOT_READ)->get();
    }
}
