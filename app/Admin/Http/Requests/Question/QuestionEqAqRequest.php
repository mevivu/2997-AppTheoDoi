<?php

namespace App\Admin\Http\Requests\Question;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\Answser\AnswerType;
use Illuminate\Validation\Rules\Enum;
use App\Enums\Question\QuestionType;

class QuestionEqAqRequest extends BaseRequest
{
    protected function methodPost()
    {
        $rules = [
            'question.question_type' => ['required', new Enum(QuestionType::class)],
            'question.question_group_id' => 'required|exists:question_groups,id',
            'question.question' => 'required',
            'answers.type' => ['required', new Enum(AnswerType::class)],
            'answers.score' => 'required',
            'answers.score.*' => 'required|min:1|max:5',
        ];

        if ($this->input('answers.type') == AnswerType::Normal->value) {
            $rules['answers.answer'] = 'required|array';
            $rules['answers.answer.*'] = 'required|distinct';
        } else {
            $rules['answers.image'] = 'required|array';
            $rules['answers.image.*'] = 'required|distinct';
        }

        return $rules;
    }

    protected function methodPut()
    {
        $rules = [
            'question.id' => 'required|exists:questions,id',
            'question.question_type' => ['required', new Enum(QuestionType::class)],
            'question.question_group_id' => 'required|exists:question_groups,id',
            'question.question' => 'required',
            'answers.type' => ['required', new Enum(AnswerType::class)],
            'answers.score' => 'required',
            'answers.score.*' => 'required|min:1|max:5',
        ];

        if ($this->input('answers.type') == AnswerType::Normal->value) {
            $rules['answers.answer'] = 'required|array';
            $rules['answers.answer.*'] = 'required|distinct';
        } else {
            $rules['answers.image'] = 'required|array';
            $rules['answers.image.*'] = 'required|distinct';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'question.question_type.required' => 'Loại câu hỏi không được để trống',
            'question.question_group_id.required' => 'Nhóm câu hỏi không được để trống',
            'question.question_group_id.exists' => 'Nhóm câu hỏi không tồn tại',
            'question.question.required' => 'Câu hỏi không được để trống',
            'answers.type.required' => 'Loại câu trả lời không được để trống',
            'answers.score.required' => 'Điểm không được để trống',
            'answers.score.*.required' => 'Điểm không được để trống',
            'answers.score.*.min' => 'Điểm không được nhỏ hơn 1',
            'answers.score.*.max' => 'Điểm không được lớn hơn 5',
            'answers.answer.required' => 'Câu trả lời không được để trống',
            'answers.answer.*.required' => 'Câu trả lời không được để trống',
            'answers.answer.*.distinct' => 'Câu trả lời không được trùng nhau',
            'answers.image.required' => 'Hình ảnh không được để trống',
            'answers.image.*.required' => 'Hình ảnh không được để trống',
            'answers.image.*.distinct' => 'Hình ảnh không được trùng nhau',
        ];
    }
}