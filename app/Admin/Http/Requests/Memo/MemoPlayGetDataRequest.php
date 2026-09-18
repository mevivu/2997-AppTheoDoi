<?php

namespace App\Admin\Http\Requests\Memo;

use App\Admin\Http\Requests\BaseRequest;

class MemoPlayGetDataRequest extends BaseRequest
{
    /**
     * Quy tắc xác thực cho phương thức GET
     */
    protected function methodGet(): array
    {
        return [
            'theme_id' => ['nullable', 'integer', 'exists:memo_themes,id'],
            'age_config_id' => ['nullable', 'integer', 'exists:memo_age_configs,id'],
        ];
    }

    /**
     * Thông báo lỗi xác thực
     */
    public function messages(): array
    {
        return [
            'theme_id.integer' => 'Mã chủ đề phải là số nguyên hợp lệ.',
            'theme_id.exists' => 'Chủ đề đã chọn không tồn tại trong hệ thống.',
            'age_config_id.integer' => 'Mã cấu hình độ tuổi phải là số nguyên hợp lệ.',
            'age_config_id.exists' => 'Cấu hình độ tuổi đã chọn không tồn tại.',
        ];
    }
}
