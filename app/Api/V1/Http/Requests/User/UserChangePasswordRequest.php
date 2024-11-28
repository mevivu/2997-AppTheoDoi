<?php

namespace App\Api\V1\Http\Requests\User;

use App\Api\V1\Http\Requests\BaseRequest;

class UserChangePasswordRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    protected function methodPost(): array
    {
        return [
            'password' => ['required', 'string', 'confirmed', 'min:6', 'max:20'],
            'password_confirmation' => ['required', 'string', 'min:6', 'max:20'],
        ];
    }
}