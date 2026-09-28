<?php

namespace App\Api\V1\Http\Requests\Lesson;

use App\Api\V1\Http\Requests\BaseRequest;

class LessonPillarRequest extends BaseRequest
{
    /**
     * Quy tắc validation khi lấy danh sách 5 trụ cột
     */
    protected function methodGet(): array
    {
        return [
            'age_group_id' => ['nullable', 'integer', 'exists:age_groups,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'age_group_id.integer' => 'ID nhóm tuổi phải là số nguyên.',
            'age_group_id.exists' => 'Nhóm tuổi không tồn tại.',
        ];
    }
}
