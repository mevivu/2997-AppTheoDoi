<?php

namespace App\Api\V1\Http\Requests\Notification;

use App\Api\V1\Http\Requests\BaseRequest;

class NotificationRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet()
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'min:1'],
        ];
    }
    protected function methodPut()
    {
        return[
            'id'=>['required','exists:App\Models\Notification,id'],
        ];
    }
    protected function methodDelete(){
        return[
            'id'=>['required','exists:App\Models\Notification,id'],
        ];
    }
}
