<?php

namespace App\Api\V1\Http\Requests\Package;

use App\Api\V1\Http\Requests\BaseRequest;


class PackageRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'id' => 'required|exists:packages,id',
            'payment_confirmation_image' => 'required|file|image|max:2048',
        ];
    }
}
