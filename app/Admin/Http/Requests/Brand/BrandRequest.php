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
            'status' => ['required', new EnumValue(BrandStatus::class, false)],
        ];
    }


}
