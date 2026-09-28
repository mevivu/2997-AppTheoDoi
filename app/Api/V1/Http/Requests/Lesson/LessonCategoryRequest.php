<?php

namespace App\Api\V1\Http\Requests\Lesson;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Lesson\EducationPillar;
use Illuminate\Validation\Rules\Enum;

class LessonCategoryRequest extends BaseRequest
{
    /**
     * Quy tắc validation khi lấy danh mục kỹ năng theo nhóm tuổi & trụ cột
     */
    protected function methodGet(): array
    {
        return [
            'age_group_id' => ['required', 'integer', 'exists:age_groups,id'],
            'pillar' => ['nullable', 'string', new Enum(EducationPillar::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'age_group_id.required' => 'Vui lòng chọn nhóm tuổi.',
            'age_group_id.integer' => 'ID nhóm tuổi phải là số nguyên.',
            'age_group_id.exists' => 'Nhóm tuổi không tồn tại.',
            'pillar.enum' => 'Trụ cột giáo dục không hợp lệ (hỗ trợ: pq, iq, eq, aq, thai_giao).',
        ];
    }
}
