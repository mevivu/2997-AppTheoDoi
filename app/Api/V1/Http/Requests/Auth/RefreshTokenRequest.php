<?php

namespace App\Api\V1\Http\Requests\Auth;

use App\Api\V1\Http\Requests\BaseRequest;

class RefreshTokenRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'refresh_token' => 'required',
        ];
    }
}
