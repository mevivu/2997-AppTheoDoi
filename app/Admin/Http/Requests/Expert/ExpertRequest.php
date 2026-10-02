<?php

namespace App\Admin\Http\Requests\Expert;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\DefaultStatus;
use Illuminate\Validation\Rules\Enum;

class ExpertRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'council_type' => ['required', new Enum(\App\Enums\Expert\ExpertCouncilType::class)],
            'name' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'hospital' => ['nullable', 'string', 'max:255'],
            'workplace' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable'],
            'bio' => ['nullable', 'string'],
            'contact_link' => ['nullable', 'string', 'max:500'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'is_verified' => ['nullable'],
            'sort_order' => ['nullable', 'integer'],
            'status' => ['required', new Enum(DefaultStatus::class)],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\Expert,id'],
            'council_type' => ['required', new Enum(\App\Enums\Expert\ExpertCouncilType::class)],
            'name' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'hospital' => ['nullable', 'string', 'max:255'],
            'workplace' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable'],
            'bio' => ['nullable', 'string'],
            'contact_link' => ['nullable', 'string', 'max:500'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'is_verified' => ['nullable'],
            'sort_order' => ['nullable', 'integer'],
            'status' => ['required', new Enum(DefaultStatus::class)],
        ];
    }
}
