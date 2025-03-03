<?php

namespace App\Admin\Http\Requests\Guide;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\Guide\GuideType;
use Illuminate\Validation\Rules\Enum;


class GuideRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'status' => ['required', new Enum(ActiveStatus::class, false)],
            'type' => ['required', new Enum(GuideType::class, false), 'unique:guides,type'],
            'steps' => ['nullable', 'array'],
            'steps.*.title' => ['required', 'string'],
            'steps.*.description' => ['nullable', 'string'],
            'steps.*.order' => ['required', 'integer'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:guides,id'],
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'status' => ['required', new Enum(ActiveStatus::class)],
            'type' => [
                'required',
                new Enum(GuideType::class),
                'unique:guides,type,' . request()->id
            ],
            'steps' => ['nullable', 'array'],
            'steps.*.title' => ['required', 'string'],
            'steps.*.description' => ['nullable', 'string'],
            'steps.*.order' => ['required', 'integer'],
        ];
    }

}
