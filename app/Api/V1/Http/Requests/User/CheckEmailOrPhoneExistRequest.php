<?php

namespace App\Api\V1\Http\Requests\User;

use App\Api\V1\Http\Requests\BaseRequest;

class CheckEmailOrPhoneExistRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
        ];
    }

    protected function methodGet(): array
    {
        return [
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'email.email' => 'Email không đúng định dạng.',
        ];
    }
}
