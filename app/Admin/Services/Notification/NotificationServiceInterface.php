<?php

namespace App\Admin\Services\Notification;

use Illuminate\Http\Request;
interface NotificationServiceInterface
{


    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);

    public function updateDeviceToken($request);

    public function getNotifications(Request $request);

    public function updateStatus(Request $request);

    public function actionMultipleRecode(Request $request): bool;

}
