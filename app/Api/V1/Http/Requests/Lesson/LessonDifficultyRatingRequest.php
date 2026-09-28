<?php

namespace App\Api\V1\Http\Requests\Lesson;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Lesson\LessonDifficultyRating;
use Illuminate\Validation\Rules\Enum;

class LessonDifficultyRatingRequest extends BaseRequest
{
    /**
     * Quy tắc validation khi đánh giá / toggle độ khó bài học
     */
    protected function methodPost(): array
    {
        return [
            'child_id' => ['nullable', 'integer', 'exists:children,id'],
            'difficulty_level' => ['required', 'string', new Enum(LessonDifficultyRating::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'child_id.required' => 'Vui lòng chọn hồ sơ bé để đánh giá.',
            'child_id.integer' => 'ID hồ sơ bé không hợp lệ.',
            'child_id.exists' => 'Không tìm thấy hồ sơ bé.',
            'difficulty_level.required' => 'Vui lòng chọn mức đánh giá.',
            'difficulty_level.string' => 'Mức đánh giá không hợp lệ.',
            'difficulty_level.enum' => 'Mức đánh giá phải là: easy, with_help hoặc hard.',
        ];
    }
}
