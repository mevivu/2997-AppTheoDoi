<?php

namespace App\Admin\Http\Requests\Quiz;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\Question\QuestionType;
use Illuminate\Validation\Rules\Enum;


class QuizRequest extends BaseRequest
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
            'age' => ['required', 'numeric'],
            'type' => ['required', new Enum(QuestionType::class)],
            'description' => ['nullable', 'string'],
            'question_ids' => ['required', 'array'],
            'question_ids.*' => ['exists:questions,id'],
        ];
    }

    protected function methodPut(): array
    {

        return [
            'id' => ['required', 'exists:App\Models\Quiz,id'],
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'age' => ['required', 'numeric'],
            'question_ids' => ['required', 'array'],
            'question_ids.*' => ['exists:questions,id'],
            'status' => ['required', new Enum(ActiveStatus::class)],


        ];
    }
}
