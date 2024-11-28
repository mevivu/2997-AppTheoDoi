<?php

namespace App\Admin\Repositories\Notification;
use App\Admin\Repositories\EloquentRepository;
use App\Enums\Notification\NotificationStatus;
use App\Models\Notification;

class NotificationRepository extends EloquentRepository implements NotificationRepositoryInterface
{

    public function getModel(): string
    {
        return Notification::class;
    }

    public function searchAllLimit($keySearch = '', $meta = [], $limit = 10)
    {
        // TODO: Implement searchAllLimit() method.
    }

    public function getByAdminId($adminId, $limit = 10)
    {
        return $this->model
            ->where('admin_id', $adminId)
            ->where('status', NotificationStatus::NOT_READ)
            ->orderBy('created_at', 'desc')
            ->take($limit);
    }

}
