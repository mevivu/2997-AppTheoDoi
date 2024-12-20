<?php

namespace App\Admin\Http\Requests\Journal;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Journal\JournalType;
use Illuminate\Validation\Rules\Enum;


class JournalRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'child_id' => ['required', 'exists:App\Models\Child,id'],
            'title' => ['required', 'string'],
            'content' => ['required', 'string'],
            'type' => ['nullable', new Enum(JournalType::class)],
            'image' => ['required', 'array', 'min:1'],
            'image.*' => ['required', 'string', 'distinct', 'not_in:""'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\Journal,id'],
            'child_id' => ['required', 'exists:App\Models\Child,id'],
            'title' => ['required', 'string'],
            'content' => ['required', 'string'],
            'type' => ['nullable', new Enum(JournalType::class)],
            'image' => ['required', 'array', 'min:1'],
            'image.*' => ['required', 'string', 'distinct', 'not_in:""'],
        ];
    }
}
