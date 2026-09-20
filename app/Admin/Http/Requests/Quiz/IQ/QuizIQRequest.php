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

    protected function prepareForValidation(): void
    {
        if ($this->has('selected_questions') && is_string($this->selected_questions)) {
            $decoded = json_decode($this->selected_questions, true);
            if (is_array($decoded)) {
                $this->merge([
                    'selected_questions' => $decoded
                ]);
            }
        }
    }

    protected function methodPut(): array
    {
        $quizId = $this->route('id') ?: $this->input('id');
        $selectedQuestions = is_array($this->selected_questions) 
            ? $this->selected_questions 
            : json_decode($this->selected_questions ?? '[]', true);

        return [
            'id' => ['required', 'exists:App\Models\Quiz,id'],
            'title' => ['required', 'string'],
            'type' => ['required', new Enum(QuestionType::class)],
            'description' => ['nullable', 'string'],
            'age' => ['nullable', 'numeric', new UniqueQuiz(request()->type, $quizId)],
            'selected_questions' => [
                'required',
                'array',
                function ($attribute, $value, $fail) use ($selectedQuestions) {
                    $quizType = request()->type;
                    $isIq = $quizType === QuestionType::IQ->value || (is_object($quizType) && $quizType->value === QuestionType::IQ->value);
                    if ($isIq) {
                        $count = is_array($selectedQuestions) ? count($selectedQuestions) : 0;
                        if ($count < 12 || $count > 15) {
                            $fail('Bài kiểm tra IQ phải có từ 12 đến 15 câu hỏi.');
                        }
                    }
                },
            ],
            'selected_questions.*' => ['exists:questions,id'],
            'status' => ['required', new Enum(ActiveStatus::class)],
            'age_group' => ['nullable', new Enum(AgeGroup::class)]
        ];
    }

    public function messages(): array
    {
        return [
            'selected_questions.required' => 'Bạn phải chọn ít nhất một câu hỏi cho bài kiểm tra.',
            'selected_questions.array' => 'Danh sách câu hỏi không hợp lệ.',
            'selected_questions.*.exists' => 'Câu hỏi được chọn không tồn tại trong hệ thống.',
            'title.required' => 'Tiêu đề bài kiểm tra không được để trống.',
            'status.required' => 'Trạng thái bài kiểm tra không được để trống.',
        ];
    }

}
