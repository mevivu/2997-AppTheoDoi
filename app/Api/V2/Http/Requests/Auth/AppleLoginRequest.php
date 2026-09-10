<?php

namespace App\Api\V2\Http\Requests\Auth;

use App\Api\V1\Http\Requests\BaseRequest;

class AppleLoginRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'apple_id' => 'required|string|max:191',
            'email' => 'nullable|email',
            'fullname' => 'nullable|string|max:191',
            'identity_token' => 'nullable|string',
            'device_token' => 'nullable|string',
            'device_id' => 'nullable|string|max:191',
            'device_name' => 'nullable|string|max:191',
            'referral_code' => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'apple_id.required' => 'Mã định danh Apple (apple_id) không được để trống.',
            'apple_id.string' => 'Apple id phải là chuỗi.',
            'apple_id.max' => 'Apple id không được quá 191 ký tự.',
            'email.email' => 'Email không đúng định dạng.',
            'fullname.max' => 'Họ và tên không được quá 191 ký tự.',
            'device_token.string' => 'Device token phải là chuỗi.',
            'device_id.string' => 'Device id phải là chuỗi.',
            'device_id.max' => 'Device id không được quá 191 ký tự.',
            'device_name.string' => 'Device name phải là chuỗi.',
            'device_name.max' => 'Device name không được quá 191 ký tự.',
        ];
    }
}
