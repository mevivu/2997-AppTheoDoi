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
            'age_group' => ['nullable', new Enum(AgeGroup::class)]
        ];
    }

    protected function methodPut(): array
    {
        $quizId = $this->route('id');
        $data = $this->all();
        $selectedQuestions = json_decode($data['selected_questions'] ?? '[]', true);
        return [
            'id' => ['required', 'exists:App\Models\Quiz,id'],
            'title' => ['required', 'string'],
            'type' => ['required', new Enum(QuestionType::class)],
            'description' => ['nullable', 'string'],
            'age' => ['nullable', 'numeric', new UniqueQuiz(request()->type, $quizId)],
            'selected_questions.*' => ['exists:questions,id'],
            'selected_questions' => [
                'required',
                function ($attribute, $value, $fail) use ($selectedQuestions) {
                    if ((request()->type === QuestionType::IQ->value ) && count($selectedQuestions) < 1) {
                        $fail('Bài kiểm tra phải chọn ít nhất 1 câu hỏi.');
                    }
                    if (request()->type === QuestionType::IQ->value && count($selectedQuestions) !== 15) {
                        $fail('Bài kiểm tra IQ phải có đúng 15 câu hỏi.');
                    }
                },
            ],

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
