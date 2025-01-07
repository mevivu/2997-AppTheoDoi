<?php

namespace App\Admin\Http\Requests\Brand;

use App\Admin\Http\Requests\BaseRequest;
use BenSampo\Enum\Rules\EnumValue;
use App\Enums\Brand\BrandStatus;

class BrandRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost()
    {
        return [
            'name' => ['required', 'string'],
            'description' => ['nullable', 'array'],
            'description.*' => ['required', 'string'],
            'country' => ['nullable', 'string'],
            'status' => ['required', new EnumValue(BrandStatus::class, false)],
        ];
    }

    protected function methodPut()
    {
        return [
            'id' => ['required', 'exists:App\Models\Brand,id'],
            'name' => ['required', 'string'],
            'description' => ['nullable'],
            'country' => ['nullable', 'string'],
            'status' => ['required', new EnumValue(BrandStatus::class, false)],
        ];
    }
}
