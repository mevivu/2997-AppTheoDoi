<?php

namespace App\Api\V1\Http\Requests\Journal;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\Journal\JournalType;
use Illuminate\Validation\Rules\Enum;


class JournalRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet(): array
    {
        return [
            'limit' => 'required|integer|min:1',
            'page' => 'required|integer|min:1',
            'type' => ['required', new Enum(JournalType::class)],
            'child_id' => ['required','numeric', 'exists:children,id'],

        ];
    }

    protected function methodPost(): array
    {
        return [
            'child_id' => 'required|integer|exists:children,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => ['required', new Enum(JournalType::class)],
            'image' => 'required|array|min:1',
            'image.*' => 'file|image|max:5000',
        ];
    }


}
