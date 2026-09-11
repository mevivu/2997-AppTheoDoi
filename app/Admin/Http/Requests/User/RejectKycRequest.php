<?php

namespace App\Admin\Http\Requests\User;

use App\Admin\Http\Requests\BaseRequest;

class RejectKycRequest extends BaseRequest
{
    /**
     * Xác định quyền
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Quy tắc validation khi từ chối duyệt hồ sơ CCCD
     */
    protected function methodPost(): array
    {
        return [
            'reason' => ['required', 'string', 'max:500'],
        ];
    }

    /**
     * Thông báo lỗi validation
     */
    public function messages(): array
    {
        return [
            'reason.required' => __('Vui lòng nhập hoặc chọn lý do từ chối để gửi thông báo cho đối tác.'),
            'reason.string' => __('Lý do từ chối phải là chuỗi văn bản hợp lệ.'),
            'reason.max' => __('Lý do từ chối không được vượt quá :max ký tự.', ['max' => 500]),
        ];
    }
}
