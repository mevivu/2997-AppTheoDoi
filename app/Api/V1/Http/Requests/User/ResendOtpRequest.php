<?php

namespace App\Api\V1\Http\Requests\User;

use App\AES\AESHelper;
use App\Api\V1\Http\Requests\BaseRequest;
use App\Models\User;

class ResendOtpRequest extends BaseRequest
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
                    if (!User::where('email', $email)->exists()) {
                        $fail('Email không tồn tại.');
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
        ];
    }
}