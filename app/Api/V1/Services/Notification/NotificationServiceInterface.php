<?php

namespace App\Api\V1\Services\Notification;

use Illuminate\Http\Request;

interface NotificationServiceInterface
{

    public function getNotificationByUser(Request $request): bool|object;

    public function updateStatusIsRead(Request $request): bool;

    public function updateAllStatusIsRead(Request $request): bool;

    public function delete(Request $request): bool;
}
