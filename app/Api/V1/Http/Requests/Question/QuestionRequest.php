<?php

namespace App\Api\V1\Http\Requests\Question;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Question\QuestionType;
use Illuminate\Validation\Rules\Enum;

class QuestionRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet()
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'min:1'],
            'question_type' => ['nullable', new Enum(QuestionType::class)],
        ];
    }


}
