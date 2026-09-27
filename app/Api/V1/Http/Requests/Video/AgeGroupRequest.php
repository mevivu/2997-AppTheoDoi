<?php

namespace App\Api\V1\Http\Requests\Video;

use App\Api\V1\Http\Requests\BaseRequest;

class AgeGroupRequest extends BaseRequest
{
    /**
     * Quy tắc validation khi gọi method GET
     */
    protected function methodGet(): array
    {
        return [
            'child_id' => ['nullable', 'integer', 'min:1'],
            'child_age_months' => ['nullable', 'integer', 'min:0', 'max:240'],
        ];
    }

    public function messages(): array
    {
        return [
            'child_id.integer' => 'ID của trẻ phải là số nguyên.',
            'child_age_months.integer' => 'Số tháng tuổi của trẻ phải là số nguyên.',
            'child_age_months.min' => 'Số tháng tuổi của trẻ không được nhỏ hơn 0.',
            'child_age_months.max' => 'Số tháng tuổi của trẻ không được vượt quá 240 tháng (20 tuổi).',
        ];
    }
}
