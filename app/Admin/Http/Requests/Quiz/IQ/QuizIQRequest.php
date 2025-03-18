<?php

namespace App\Admin\Http\Requests\Quiz\IQ;

use App\Admin\Http\Requests\BaseRequest;
use App\Admin\Rules\UniqueQuiz;
use App\Enums\ActiveStatus;
use App\Enums\Question\AgeGroup;
use App\Enums\Question\QuestionType;
use Illuminate\Validation\Rules\Enum;


class QuizIQRequest extends BaseRequest
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
            'question_ids' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                   $count = count($value);
                    if (request()->type === QuestionType::IQ->value && count($value) < 15) {
                        $fail('Bài kiểm tra phải có đủ 15 câu hỏi.');
                    }
                    if ((request()->type === QuestionType::EQ->value || request()->type === QuestionType::AQ->value) && count($value) < 1) {
                        $fail('Bài kiểm tra phải chọn ít nhất 1 câu hỏi.');
                    }
                },
            ],
            'question_ids.*' => ['exists:questions,id'],
            'selected_questions' => ['required'],
            'age_group' => ['nullable', new Enum(AgeGroup::class)]
        ];
    }

    protected function methodPut(): array
    {
        $quizId = $this->route('id');
        return [
            'id' => ['required', 'exists:App\Models\Quiz,id'],
            'title' => ['required', 'string'],
            'type' => ['required', new Enum(QuestionType::class)],
            'description' => ['nullable', 'string'],
            'age' => ['nullable', 'numeric', new UniqueQuiz(request()->type, $quizId)],
            'question_ids' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    if (request()->type === QuestionType::IQ->value && count($value) < 15) {
                        $fail('Bài kiểm tra phải có đủ 15 câu hỏi.');
                    }
                    if ((request()->type === QuestionType::EQ->value || request()->type === QuestionType::AQ->value) && count($value) < 1) {
                        $fail('Bài kiểm tra phải chọn ít nhất 1 câu hỏi.');
                    }
                },
            ],
            'question_ids.*' => ['exists:questions,id'],
            'selected_questions' => ['required'],
            'status' => ['required', new Enum(ActiveStatus::class)],
            'age_group' => ['nullable', new Enum(AgeGroup::class)]
        ];
    }

    public function messages(): array
    {
        return [
            'question_ids.min' => 'Bài kiểm tra phải có đủ 15 câu hỏi.',
            'question_ids.required' => 'Bạn phải chọn ít nhất một câu hỏi cho bài kiểm tra.',
            'question_ids.*.exists' => 'Câu hỏi được chọn không tồn tại.'
        ];
    }

}
