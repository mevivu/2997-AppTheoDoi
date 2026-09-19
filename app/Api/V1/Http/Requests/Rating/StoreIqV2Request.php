<?php

namespace App\Api\V1\Http\Requests\Rating;

use App\Api\V1\Http\Requests\BaseRequest;

class StoreIqV2Request extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'child_id' => 'required|integer|exists:children,id',
            'rating_id' => 'required|integer|exists:ratings,id',
            'quiz_id' => 'required|integer|exists:quizzes,id',
            'tag' => 'required|string',
            'game_score' => 'required|integer|min:0',
            'game_duration_spent' => 'nullable|integer|min:0',
            'game_pairs_matched' => 'nullable|integer|min:0',
            'game_mistakes' => 'nullable|integer|min:0',
            'memo_theme_id' => 'nullable|integer|exists:memo_themes,id',
            'memo_age_config_id' => 'nullable|integer|exists:memo_age_configs,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|integer|exists:questions,id',
            'answers.*.answer_id' => 'required|integer|exists:answers,id',
        ];
    }

    public function messages(): array
    {
        return [
            'child_id.required' => 'Vui lòng nhập ID của trẻ.',
            'child_id.exists' => 'ID của trẻ không tồn tại.',
            'rating_id.required' => 'Vui lòng cung cấp ID của đánh giá.',
            'rating_id.integer' => 'ID của đánh giá phải là một số nguyên.',
            'rating_id.exists' => 'ID của đánh giá không tồn tại.',
            'quiz_id.required' => 'Vui lòng cung cấp ID của bài kiểm tra.',
            'quiz_id.integer' => 'ID bài kiểm tra phải là một số nguyên.',
            'quiz_id.exists' => 'ID bài kiểm tra không tồn tại.',
            'tag.required' => 'Vui lòng nhập thẻ.',
            'tag.string' => 'Thẻ phải là một chuỗi.',
            'game_score.required' => 'Vui lòng cung cấp điểm game.',
            'game_score.integer' => 'Điểm game phải là số nguyên.',
            'game_score.min' => 'Điểm game không được nhỏ hơn 0.',
            'answers.required' => 'Vui lòng nhập câu trả lời.',
            'answers.array' => 'Câu trả lời phải là một mảng.',
            'answers.*.question_id.required' => 'Vui lòng cung cấp ID của câu hỏi.',
            'answers.*.question_id.exists' => 'ID câu hỏi không tồn tại.',
            'answers.*.answer_id.required' => 'Vui lòng cung cấp ID của câu trả lời.',
            'answers.*.answer_id.exists' => 'ID câu trả lời không tồn tại.',
        ];
    }
}
