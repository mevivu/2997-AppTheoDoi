<?php

namespace App\Api\V1\Http\Requests\Support;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Support\SupportType;
use Illuminate\Validation\Rules\Enum;

class SupportRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet()
    {
        return [
            'page' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'type' => ['required', new Enum(SupportType::class)],
        ];
    }

    public function messages()
    {
        return [
            'type.required' => 'Loại hỗ trợ không được để trống',
            'type.enum' => 'Loại hỗ trợ không hợp lệ',
        ];
    }
}