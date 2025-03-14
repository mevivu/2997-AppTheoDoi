<?php

namespace App\Admin\Http\Requests\Question;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\Answser\AnswerType;
use Illuminate\Validation\Rules\Enum;
use App\Enums\Question\QuestionType;

class QuestionIqRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        $rules = [
            'question.question_type' => ['required', new Enum(QuestionType::class)],
            'question.age' => 'required|numeric',
            'question.question_image' => 'nullable',
            'question.question' => 'required',
            'question.status' => ['required', new Enum(ActiveStatus::class)],
            'answers.type' => ['required', new Enum(AnswerType::class)],
            'answers.is_correct' => 'required',
        ];

        if ($this->input('answers.type') == AnswerType::Normal->value) {
            $rules['answers.answer'] = 'required|array';
            $rules['answers.answer.*'] = 'required|distinct';
        } else {
            $rules['answers.image'] = 'required';
            $rules['answers.image.*'] = 'required|distinct';
        }

        return $rules;
    }

    protected function methodPut(): array
    {
        $rules = [
            'question.id' => 'required|exists:questions,id',
            'question.question_type' => ['required', new Enum(QuestionType::class)],
            'question.age' => 'required|numeric',
            'question.question' => 'required',
            'question.status' => ['required', new Enum(ActiveStatus::class)],
            'answers.type' => ['required', new Enum(AnswerType::class)],
            'answers.is_correct' => 'required',
            'question.question_image' => 'nullable',
        ];

        if ($this->input('answers.type') == AnswerType::Normal->value) {
            $rules['answers.answer'] = 'required|array';
            $rules['answers.answer.*'] = 'required|distinct';
        } else {
            $rules['answers.image'] = 'required';
            $rules['answers.image.*'] = 'required|distinct';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'question.question_type.required' => 'Loại câu hỏi không được để trống',
            'question.question_type.enum' => 'Loại câu hỏi không hợp lệ',
            'question.age.required' => 'Tuổi không được để trống',
            'question.age.numeric' => 'Tuổi phải là số',
            'question.question.required' => 'Câu hỏi không được để trống',
            'question.status.required' => 'Trạng thái không được để trống',
            'question.status.enum' => 'Trạng thái không hợp lệ',
            'answers.type.required' => 'Loại câu trả lời không được để trống',
            'answers.type.enum' => 'Loại câu trả lời không hợp lệ',
            'answers.answer.required' => 'Câu trả lời không được để trống',
            'answers.answer.array' => 'Câu trả lời phải là mảng',
            'answers.answer.*.required' => 'Câu trả lời không được để trống',
            'answers.answer.*.distinct' => 'Câu trả lời không được trùng nhau',
            'answers.is_correct.required' => 'Câu trả lời đúng không được để trống',
        ];
    }
}
