<?php

namespace App\Api\V1\Http\Requests\Quiz;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Api\V1\Rules\ValidChild;
use App\Enums\Question\QuestionType;
use Illuminate\Validation\Rules\Enum;

class QuizEQAndAQRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet(): array
    {
        return [
            'child_id' => ['required', 'numeric', new ValidChild()],
            'type' => ['required', new Enum(QuestionType::class)],
        ];
    }

}
