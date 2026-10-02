<?php

namespace App\Admin\Http\Requests\ExpertCategory;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\DefaultStatus;
use Illuminate\Validation\Rules\Enum;

class ExpertCategoryRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['nullable'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'status' => ['required', new Enum(DefaultStatus::class)],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\ExpertCategory,id'],
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['nullable'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'status' => ['required', new Enum(DefaultStatus::class)],
        ];
    }
}
