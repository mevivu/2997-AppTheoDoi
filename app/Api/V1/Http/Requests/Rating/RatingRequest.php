<?php

namespace App\Api\V1\Http\Requests\Rating;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\Journal\JournalType;
use App\Enums\Question\QuestionType;
use Illuminate\Validation\Rules\Enum;


class RatingRequest extends BaseRequest
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
            'child_id' => ['required', 'numeric', 'exists:children,id'],
            'type' => ['required', new Enum(QuestionType::class)],

        ];
    }

    protected function methodPost(): array
    {
        return [
            'child_id' => 'required|integer|exists:children,id',
            'tag' => 'required|string',
            'type' => ['required', new Enum(QuestionType::class)],
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|integer|exists:questions,id',
            'answers.*.answer_id' => 'required|integer|exists:answers,id'
        ];
    }


}
