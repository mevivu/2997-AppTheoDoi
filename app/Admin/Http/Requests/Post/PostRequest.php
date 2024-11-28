<?php

namespace App\Admin\Http\Requests\Post;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\FeaturedStatus;
use App\Enums\Post\PostStatus;
use Illuminate\Validation\Rules\Enum;

class PostRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'categories_id' => ['nullable', 'array'],
            'categories_id.*' => ['nullable', 'exists:App\Models\PostCategory,id'],
            'title' => ['required', 'string'],
            'image' => ['required'],
            'is_featured' => ['nullable', new Enum(FeaturedStatus::class)],
            'status' => ['required', new Enum(PostStatus::class)],
            'excerpt' => ['nullable'],
            'content' => ['nullable']
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\Post,id'],
            'categories_id' => ['nullable', 'array'],
            'categories_id.*' => ['nullable', 'exists:App\Models\PostCategory,id'],
            'title' => ['required', 'string'],
            'image' => ['required'],
            'is_featured' => ['nullable', new Enum(FeaturedStatus::class)],
            'status' => ['required', new Enum(PostStatus::class)],
            'excerpt' => ['nullable'],
            'content' => ['nullable']
        ];
    }
}
