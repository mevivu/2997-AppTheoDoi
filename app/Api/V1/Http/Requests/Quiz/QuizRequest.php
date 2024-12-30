<?php

namespace App\Api\V1\Http\Requests\Quiz;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Question\QuestionType;
use Illuminate\Validation\Rules\Enum;

class QuizRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet()
    {
        return [
            'age' => 'required|integer|min:1',
        ];
    }
    protected function methodPost()
    {
        return [
            'age' => 'required|integer|min:1',
        ];
    }
}
