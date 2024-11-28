<?php

namespace App\Admin\Http\Requests\PostCategory;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\PostCategory\PostCategoryStatus;
use BenSampo\Enum\Rules\EnumValue;
use App\Admin\Rules\Category\CategoryParent;

class PostCategoryRequest extends BaseRequest
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
            'desc' => ['required', 'string'],
            'avatar' => ['required', 'string'],
            'parent_id' => ['nullable', 'exists:App\Models\PostCategory,id'],
            'position' => ['nullable', 'integer'],
            'status' => ['required', new EnumValue(PostCategoryStatus::class, false)]
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\PostCategory,id'],
            'desc' => ['required', 'string'],
            'name' => ['required', 'string'],
            'position' => ['nullable', 'integer'],
            'avatar' => ['required', 'string'],
            'status' => ['required', new EnumValue(PostCategoryStatus::class, false)]
        ];
    }
}
