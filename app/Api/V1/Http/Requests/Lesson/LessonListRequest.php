<?php

namespace App\Api\V1\Http\Requests\Lesson;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Lesson\EducationPillar;
use App\Enums\Lesson\LessonDifficulty;
use Illuminate\Validation\Rules\Enum;

class LessonListRequest extends BaseRequest
{
    /**
     * Quy tắc validation khi lấy danh sách bài học
     */
    protected function methodGet(): array
    {
        return [
            'category_id' => ['nullable', 'integer', 'exists:lesson_categories,id'],
            'lesson_category_id' => ['nullable', 'integer', 'exists:lesson_categories,id'],
            'age_group_id' => ['nullable', 'integer', 'exists:age_groups,id'],
            'pillar' => ['nullable', 'string', new Enum(EducationPillar::class)],
            'access_type' => ['nullable', 'string', 'in:free,vip,all'],
            'difficulty' => ['nullable', 'string', new Enum(LessonDifficulty::class)],
            'keyword' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'Danh mục bài học không tồn tại.',
            'lesson_category_id.exists' => 'Danh mục bài học không tồn tại.',
            'age_group_id.exists' => 'Nhóm độ tuổi không tồn tại.',
            'pillar.enum' => 'Trụ cột giáo dục không hợp lệ.',
            'access_type.in' => 'Loại truy cập không hợp lệ (free, vip hoặc all).',
            'difficulty.enum' => 'Độ khó không hợp lệ (easy, medium hoặc hard).',
            'keyword.max' => 'Từ khóa tìm kiếm không được vượt quá 100 ký tự.',
            'page.min' => 'Số trang phải lớn hơn hoặc bằng 1.',
            'limit.min' => 'Số lượng trên trang phải từ 1 đến 50.',
            'limit.max' => 'Số lượng trên trang không được vượt quá 50.',
        ];
    }
}
