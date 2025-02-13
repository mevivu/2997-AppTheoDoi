<?php

namespace App\Admin\Http\Requests\Notification;

use App\Admin\Http\Requests\BaseRequest;


class NotificationStatusRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'admin_id' => 'required|exists:admins,id',
        ];
    }




}
