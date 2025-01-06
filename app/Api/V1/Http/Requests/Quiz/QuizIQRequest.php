<?php

namespace App\Api\V1\Http\Requests\Quiz;

use App\Api\V1\Http\Requests\BaseRequest;

class QuizIQRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet(): array
    {
        return [
            'age' => 'required|integer|min:1',
        ];
    }
    protected function methodPost(): array
    {
        return [
            'age' => 'required|integer|min:1',
        ];
    }
}
