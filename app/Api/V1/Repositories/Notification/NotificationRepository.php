<?php

namespace App\Api\V1\Repositories\Notification;
use \App\Admin\Repositories\Notification\NotificationRepository as AdminArea;
use App\Admin\Repositories\Notification\NotificationRepositoryInterface;
use App\Models\Notification;

class NotificationRepository extends AdminArea implements NotificationRepositoryInterface
{
    protected $model;

    public function __construct(Notification $note)
    {
        $this->model = $note;
    }

    public function getNotificationByUserId($role, $userId, $limit = 10)
    {
        if ($userId) {
            return $this->model->where($role, $userId)
                ->orderBy('id', 'desc')
                ->paginate($limit);
        }

        return false;
    }

}
