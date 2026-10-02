<?php

namespace App\Admin\Http\Requests\ExpertPost;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\DefaultStatus;
use Illuminate\Validation\Rules\Enum;

class ExpertPostRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'expert_id' => ['nullable', 'exists:App\Models\Expert,id'],
            'category_id' => ['nullable', 'exists:App\Models\ExpertCategory,id'],
            'age_group_id' => ['nullable', 'exists:App\Models\AgeGroup,id'],
            'image' => ['nullable'],
            'reading_time' => ['nullable', 'string', 'max:50'],
            'excerpt' => ['nullable', 'string'],
            'expert_quote' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'is_featured' => ['nullable'],
            'sort_order' => ['nullable', 'integer'],
            'status' => ['required', new Enum(DefaultStatus::class)],
            'posted_at' => ['nullable', 'date'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\ExpertPost,id'],
            'title' => ['required', 'string', 'max:255'],
            'expert_id' => ['nullable', 'exists:App\Models\Expert,id'],
            'category_id' => ['nullable', 'exists:App\Models\ExpertCategory,id'],
            'age_group_id' => ['nullable', 'exists:App\Models\AgeGroup,id'],
            'image' => ['nullable'],
            'reading_time' => ['nullable', 'string', 'max:50'],
            'excerpt' => ['nullable', 'string'],
            'expert_quote' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'is_featured' => ['nullable'],
            'sort_order' => ['nullable', 'integer'],
            'status' => ['required', new Enum(DefaultStatus::class)],
            'posted_at' => ['nullable', 'date'],
        ];
    }
}
