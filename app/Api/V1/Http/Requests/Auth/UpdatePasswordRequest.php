<?php

namespace App\Api\V1\Http\Requests\Auth;

use App\Api\V1\Http\Requests\BaseRequest;

class UpdatePasswordRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    protected function methodPut(): array
    {
        return [
            'old_password' => ['required', 'string', 'current_password:api'],
            'password' => ['required', 'string', 'confirmed'],
        ];
    }

    public function messages()
    {
        return [
            'old_password.required' => 'Mật khẩu cũ không được để trống.',
            'password.required' => 'Mật khẩu mới không được để trống.',
            'password.confirmed' => 'Mật khẩu mới không trùng khớp.',
        ];
    }
}