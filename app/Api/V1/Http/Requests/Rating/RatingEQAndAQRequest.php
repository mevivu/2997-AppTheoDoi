<?php

namespace App\Api\V1\Http\Requests\Rating;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Api\V1\Rules\ValidChild;
use App\Enums\Question\QuestionType;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;


class RatingEQAndAQRequest extends BaseRequest
{


    protected function methodPost(): array
    {
        return [
            'child_id' => ['required', 'integer', new ValidChild()],
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|integer|exists:questions,id',
            'answers.*.answer_id' => 'required|integer|exists:answers,id',
            'type' => ['required', new Enum(QuestionType::class)],

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        Log::error('Validation errors in RatingRequest', [
            'errors' => $errors->messages()
        ]);

        throw new ValidationException($validator);
    }

}
