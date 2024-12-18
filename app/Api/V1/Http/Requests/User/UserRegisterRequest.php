<?php

namespace App\Api\V1\Http\Requests\User;

use App\Api\V1\Http\Requests\BaseRequest;
use App\AES\AESHelper;
use App\Models\User;

class UserRegisterRequest extends BaseRequest
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
                'email',
                function ($attribute, $value, $fail) {
                    $email = AESHelper::encrypt($value);
                    if (User::where('email', $email)->exists()) {
                        $fail('Email đã được sử dụng.');
                    }
                }
            ],
            'fullname' => ['required'],
            'password' => ['required', 'string', 'confirmed', 'min:6', 'max:20'],
            'phone' => [
                'required',
                'regex:/((09|03|07|08|05)+([0-9]{8})\b)/',
                function ($attribute, $value, $fail) {
                    $phone = AESHelper::encrypt($value);
                    if (User::where('phone', $phone)->exists()) {
                        $fail('Số điện thoại được sử dụng.');
                    }
                }
            ],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email không đúng định dạng.',
            'fullname.required' => 'Tên không được để trống.',
            'password.required' => 'Mật khẩu không được để trống.',
            'password.string' => 'Mật khẩu phải là chuỗi.',
            'password.confirmed' => 'Mật khẩu không khớp.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.max' => 'Mật khẩu không được quá 20 ký tự.',
            'phone.required' => 'Số điện thoại không được để trống.',
            'phone.regex' => 'Số điện thoại không đúng định dạng.',
        ];
    }
}