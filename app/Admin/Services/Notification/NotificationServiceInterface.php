<?php

namespace App\Admin\Services\Notification;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Driver;
use App\Enums\Notification\MessageType;
interface NotificationServiceInterface
{
    /**
     * Tạo mới
     *
     * @return mixed
     * @var Illuminate\Http\Request $request
     *
     */
    public function store(Request $request);

    /**
     * Cập nhật
     *
     * @return boolean
     * @var Illuminate\Http\Request $request
     *
     */
    public function update(Request $request);

    /**
     * Xóa
     *
     * @param int $id
     *
     * @return boolean
     */
    public function delete($id);

    public function updateDeviceToken($request);

    public function getNotifications(Request $request);

    public function updateStatus(Request $request);

    public function actionMultipleRecode(Request $request): bool;

    public function sendFirebaseNotificationToUser(User $user, string $title, string $body, ?MessageType $type = null);
    public function sendFirebaseNotificationToDriver(Driver $driver, string $title, string $body, ?MessageType $type = null);
}
