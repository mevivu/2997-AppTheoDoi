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
            'question' => ['required'],
            'question_type' => ['required', new Enum(QuestionType::class)],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];

        if ($this->input('question_type') == QuestionType::IQ->value) {
            $this->validate['age'] = ['required', 'numeric'];
            $this->validate['correct_answer'] = ['required'];
            $this->validate['wrong_answers'] = ['required', 'array'];
        }

        if ($this->input('question_type') == QuestionType::EQ->value || $this->input('question_type') == QuestionType::AQ->value) {
            $this->validate['question_group_id'] = ['required', 'numeric', 'exists:App\Models\QuestionGroup,id'];
            $this->validate['answers'] = ['required', 'array'];
            $this->validate['score'] = ['required', 'numeric'];
        }

        return $this->validate;
    }

    protected function methodPut()
    {
        $this->validate = [
            'id' => ['required', 'numeric', 'exists:App\Models\Question,id'],
            'question' => ['required'],
            'question_type' => ['required', new Enum(QuestionType::class)],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];

        if ($this->input('question_type') == QuestionType::IQ->value) {
            $this->validate['age'] = ['required', 'numeric'];
            $this->validate['correct_answer'] = ['required'];
            $this->validate['wrong_answers'] = ['required', 'array'];
        }

        if ($this->input('question_type') == QuestionType::EQ->value || $this->input('question_type') == QuestionType::AQ->value) {
            $this->validate['question_group_id'] = ['required', 'exists:App\Models\QuestionGroup,id'];
            $this->validate['answers'] = ['required', 'array'];
            $this->validate['score'] = ['required', 'numeric'];
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