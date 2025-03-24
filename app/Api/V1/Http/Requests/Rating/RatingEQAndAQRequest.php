<?php

namespace App\Api\V1\Http\Requests\Rating;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Api\V1\Rules\ValidChild;
use App\Enums\Question\QuestionType;
use Illuminate\Validation\Rules\Enum;


class RatingEQAndAQRequest extends BaseRequest
{


    protected function methodPost(): array
    {
        return [
            'child_id' => ['required', 'integer', new ValidChild()],
            'answers' => 'required|array',
            'tag' => 'required',
            'answers.*.question_id' => 'required|integer|exists:questions,id',
            'answers.*.answer_id' => 'required|integer|exists:answers,id',
            'type' => ['required', new Enum(QuestionType::class)],

        ];
    }

    public function messages(): array
    {
        return [
            'child_id.required' => 'Vui lòng cung cấp ID của trẻ.',
            'child_id.integer' => 'ID của trẻ phải là một số nguyên.',
            'answers.required' => 'Vui lòng cung cấp câu trả lời.',
            'answers.array' => 'Câu trả lời phải là một mảng.',
            'tag.required' => 'Vui lòng cung cấp thẻ.',
            'answers.*.question_id.required' => 'Vui lòng cung cấp ID của câu hỏi.',
            'answers.*.question_id.integer' => 'ID câu hỏi phải là một số nguyên.',
            'answers.*.question_id.exists' => 'ID câu hỏi không tồn tại.',
            'answers.*.answer_id.required' => 'Vui lòng cung cấp ID của câu trả lời.',
            'answers.*.answer_id.integer' => 'ID câu trả lời phải là một số nguyên.',
            'answers.*.answer_id.exists' => 'ID câu trả lời không tồn tại.',
            'type.required' => 'Vui lòng chọn loại câu hỏi.',
            'type.enum' => 'Loại câu hỏi không hợp lệ.',
        ];
    }

}
