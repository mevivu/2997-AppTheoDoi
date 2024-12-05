<?php

namespace App\Admin\Http\Requests\Question;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use Illuminate\Validation\Rules\Enum;
use App\Enums\Question\QuestionType;

class QuestionRequest extends BaseRequest
{
    protected function methodPost()
    {
        $this->validate = [
            'question.question' => ['required'],
            'question.question_type' => ['required', new Enum(QuestionType::class)],
            'question.status' => ['required', new Enum(ActiveStatus::class)],
        ];

        if ($this->input('question.question_type') == QuestionType::IQ->value) {
            $this->validate['question.age'] = ['required', 'numeric'];
            $this->validate['answer.correct_answer'] = ['required'];
            $this->validate['answer.wrong_answers'] = ['required', 'array'];
        }

        if ($this->input('question.question_type') == QuestionType::EQ->value || $this->input('question.question_type') == QuestionType::AQ->value) {
            $this->validate['question.question_group_id'] = ['required', 'numeric', 'exists:App\Models\QuestionGroup,id'];
            $this->validate['answer.answers'] = ['required', 'array'];
            $this->validate['answer.scores'] = ['required', 'array'];
        }

        return $this->validate;
    }

    protected function methodPut()
    {
        $this->validate = [
            'question.id' => ['required', 'numeric', 'exists:App\Models\Question,id'],
            'question.question' => ['required'],
            'question.question_type' => ['required', new Enum(QuestionType::class)],
            'question.status' => ['required', new Enum(ActiveStatus::class)],
        ];

        if ($this->input('question.question_type') == QuestionType::IQ->value) {
            $this->validate['question.age'] = ['required', 'numeric'];
            $this->validate['answer.correct_answer_id'] = ['required', 'numeric', 'exists:App\Models\Answer,id'];
            $this->validate['answer.correct_answer'] = ['required'];
            $this->validate['answer.wrong_answers'] = ['required', 'array'];
        }

        if ($this->input('question.question_type') == QuestionType::EQ->value || $this->input('question.question_type') == QuestionType::AQ->value) {
            $this->validate['question.question_group_id'] = ['required', 'exists:App\Models\QuestionGroup,id'];
            $this->validate['answer.answers'] = ['required', 'array'];
            $this->validate['answer.scores'] = ['required', 'array'];
        }

        return $this->validate;
    }

    public function messages()
    {
        return [
            'question.required' => "Vui lòng nhập câu hỏi",
            'question_type.required' => "Vui lòng chọn loại câu hỏi",
            'status.required' => "Vui lòng chọn trạng thái",
            'age.required' => "Vui lòng nhập tuổi",
            'age.numeric' => "Tuổi phải là số",
            'correct_answer.required' => "Vui lòng nhập đáp án đúng",
            'wrong_answers.required' => "Vui lòng nhập đáp án sai",
            'wrong_answers.array' => "Đáp án sai phải là mảng",
            'question_group_id.required' => "Vui lòng chọn nhóm câu hỏi",
            'question_group_id.exists' => "Nhóm câu hỏi không tồn tại",
            'answers.required' => "Vui lòng nhập đáp án",
            'answers.array' => "Đáp án phải là mảng",
            'score.required' => "Vui lòng nhập điểm",
            'score.numeric' => "Điểm phải là số",
        ];
    }
}