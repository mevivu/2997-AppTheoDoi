<?php

namespace App\Api\V1\Http\Requests\Auth;

use App\Api\V1\Http\Requests\BaseRequest;


class RegisterRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'phone' => ['required', 'regex:/((09|03|07|08|05)+([0-9]{8})\b)/', 'unique:users,phone'],
            'fullname' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed'],
            'email' => ['required', 'email', 'unique:users,email'],
        ];
    }
}
