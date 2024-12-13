<?php

namespace App\Api\V1\Services\Notification;

use App\Models\Driver;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

interface NotificationServiceInterface
{

    public function getNotificationByUser(Request $request): bool|object;

    public function updateStatusIsRead(Request $request): bool;

    public function updateAllStatusIsRead(Request $request): bool;
}
