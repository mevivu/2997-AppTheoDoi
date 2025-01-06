<?php

namespace App\Api\V1\Http\Requests\Quiz;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Question\QuestionType;
use Illuminate\Validation\Rules\Enum;

class QuizAQAndEQRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet(): array
    {
        return [
            'type' => ['required', new Enum(QuestionType::class)],

        ];
    }

}
