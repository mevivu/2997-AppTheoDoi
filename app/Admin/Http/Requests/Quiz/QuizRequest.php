<?php

namespace App\Admin\Http\Requests\Quiz;

use App\Admin\Http\Requests\BaseRequest;
use App\Admin\Rules\UniqueQuiz;
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
            'age' => ['nullable', 'numeric', new UniqueQuiz(request()->type)],
            'type' => ['required', new Enum(QuestionType::class)],
            'description' => ['nullable', 'string'],
            'question_ids' => ['required', 'array', 'min:3'],
            'question_ids.*' => ['exists:questions,id'],
        ];
    }

    protected function methodPut(): array
    {
        $quizId = $this->route('id');
        return [
            'id' => ['required', 'exists:App\Models\Quiz,id'],
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'age' => ['nullable', 'numeric', new UniqueQuiz(request()->type, $quizId)],
            'question_ids' => ['required', 'array', 'min:3'],
            'question_ids.*' => ['exists:questions,id'],
            'status' => ['required', new Enum(ActiveStatus::class)]

        ];
    }
    public function messages(): array
    {
        return [
            'question_ids.min' => 'Bài kiểm tra phải có đủ 3 câu hỏi.',
            'question_ids.required' => 'Bạn phải chọn ít nhất một câu hỏi cho bài kiểm tra.',
            'question_ids.*.exists' => 'Câu hỏi được chọn không tồn tại.'
        ];
    }

}