<?php

namespace App\Api\V1\Http\Requests\User;

use App\Api\V1\Http\Requests\BaseRequest;

class VerificationOtp extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    protected function methodPost(): array
    {
        return [
            'otp' => 'required|numeric',
            'email' => 'required|email',
        ];
    }
}
