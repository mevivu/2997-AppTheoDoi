<?php

namespace App\Api\V1\Http\Requests\Auth;

use App\AES\AESHelper;
use App\Api\V1\Http\Requests\BaseRequest;
use App\Models\User;

class ResetPasswordRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    protected function methodPost(): array
    {
        return [
            'email' => [
                'required',
                function ($attribute, $value, $fail) {
                    $email = AESHelper::encrypt($value);
                    if (!User::where('email', $email)->exists()) {
                        $fail('Email không tồn tại.');
                    }
                }
            ]
        ];
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    protected function methodPut(): array
    {
        return [
            'password' => ['required', 'string', 'max:255', 'confirmed'],
            'email' => [
                'required',
                function ($attribute, $value, $fail) {
                    $email = AESHelper::encrypt($value);
                    if (!User::where('email', $email)->exists()) {
                        $fail('Email không tồn tại.');
                    }
                }
            ]
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email không được để trống.',
            'password.required' => 'Mật khẩu không được để trống.',
            'password.confirmed' => 'Mật khẩu không trùng khớp.',
        ];
    }
}
