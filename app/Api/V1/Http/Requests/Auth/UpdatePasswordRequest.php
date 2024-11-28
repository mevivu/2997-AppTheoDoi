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
}
