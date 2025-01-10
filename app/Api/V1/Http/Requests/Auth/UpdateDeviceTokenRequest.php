<?php

namespace App\Api\V1\Http\Requests\Auth;

use App\Api\V1\Http\Requests\BaseRequest;

class UpdateDeviceTokenRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPut(): array
    {
        return [
            'device_token' => 'required|string'
        ];
    }

    public function messages(): array
    {
        return [
            'device_token.required' => 'Device token không được để trống',
            'device_token.string' => 'Device token phải là chuỗi'
        ];
    }
}
