<?php

namespace App\Api\V1\Http\Requests\Rating;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Api\V1\Rules\ValidChild;
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
            'child_id' => ['required', 'numeric', new ValidChild()],
            'type' => ['required', new Enum(QuestionType::class)],

        ];
    }

    protected function methodPost(): array
    {
        return [
            'child_id' => 'required|integer|exists:children,id',
            'rating_id' => 'required|integer|exists:ratings,id',
            'tag' => 'required|string',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|integer|exists:questions,id',
            'answers.*.answer_id' => 'required|integer|exists:answers,id'
        ];
    }

    public function messages(): array
    {
        return [
            'limit.required' => 'Vui lòng nhập giới hạn.',
            'limit.integer' => 'Giới hạn phải là một số nguyên.',
            'limit.min' => 'Giới hạn phải lớn hơn hoặc bằng 1.',
            'page.required' => 'Vui lòng nhập trang.',
            'page.integer' => 'Trang phải là một số nguyên.',
            'page.min' => 'Trang phải lớn hơn hoặc bằng 1.',
            'child_id.numeric' => 'ID của trẻ phải là một số.',
            'child_id.exists' => 'ID của trẻ không tồn tại.',
            'rating_id.required' => 'Vui lòng cung cấp ID của đánh giá.',
            'rating_id.integer' => 'ID của đánh giá phải là một số nguyên.',
            'rating_id.exists' => 'ID của đánh giá không tồn tại.',
            'type.required' => 'Vui lòng chọn loại câu hỏi.',
            'type.enum' => 'Loại câu hỏi không hợp lệ.',
            'child_id.required' => 'Vui lòng nhập ID của trẻ.',
            'tag.required' => 'Vui lòng nhập thẻ.',
            'tag.string' => 'Thẻ phải là một chuỗi.',
            'answers.required' => 'Vui lòng nhập câu trả lời.',
            'answers.array' => 'Câu trả lời phải là một mảng.',
            'answers.*.question_id.required' => 'Vui lòng cung cấp ID của câu hỏi.',
            'answers.*.question_id.integer' => 'ID câu hỏi phải là một số nguyên.',
            'answers.*.question_id.exists' => 'ID câu hỏi không tồn tại.',
            'answers.*.answer_id.required' => 'Vui lòng cung cấp ID của câu trả lời.',
            'answers.*.answer_id.integer' => 'ID câu trả lời phải là một số nguyên.',
            'answers.*.answer_id.exists' => 'ID câu trả lời không tồn tại.',
        ];
    }


}
