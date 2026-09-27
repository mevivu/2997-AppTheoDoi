<?php

namespace App\Api\V1\Http\Requests\Video;

use App\Api\V1\Http\Requests\BaseRequest;

class VideoCategoryRequest extends BaseRequest
{
    /**
     * Quy tắc validation khi gọi method GET
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
            'age_group_id.exists' => 'Nhóm tuổi được chọn không tồn tại trên hệ thống.',
        ];
    }
}
