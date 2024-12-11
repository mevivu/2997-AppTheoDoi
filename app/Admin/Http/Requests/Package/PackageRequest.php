<?php

namespace App\Admin\Http\Requests\Package;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\Package\PackageType;
use Illuminate\Validation\Rules\Enum;


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
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'string'],
            'type' => ['required', new Enum(PackageType::class)],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\Package,id'],
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'string'],
            'type' => ['required', new Enum(PackageType::class)],
            'status' => ['required', new Enum(ActiveStatus::class)],

        ];
    }
}
