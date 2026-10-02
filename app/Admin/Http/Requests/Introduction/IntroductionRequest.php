<?php

namespace App\Admin\Http\Requests\Introduction;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\DefaultStatus;
use App\Enums\Introduction\IntroductionSectionType;
use Illuminate\Validation\Rules\Enum;

class IntroductionRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'section_type' => ['required', new Enum(IntroductionSectionType::class)],
            'image' => ['nullable'],
            'icon' => ['nullable', 'string', 'max:100'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'status' => ['required', new Enum(DefaultStatus::class)],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\Introduction,id'],
            'title' => ['required', 'string', 'max:255'],
            'section_type' => ['required', new Enum(IntroductionSectionType::class)],
            'image' => ['nullable'],
            'icon' => ['nullable', 'string', 'max:100'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'status' => ['required', new Enum(DefaultStatus::class)],
        ];
    }
}
