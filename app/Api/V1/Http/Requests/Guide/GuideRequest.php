<?php

namespace App\Api\V1\Http\Requests\Guide;

use App\Api\V1\Http\Requests\BaseRequest;

use App\Enums\Guide\GuideType;

use Illuminate\Validation\Rules\Enum;

class GuideRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet()
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'min:1'],
            'type' => ['nullable', new Enum(GuideType::class)],
        ];
    }
}
