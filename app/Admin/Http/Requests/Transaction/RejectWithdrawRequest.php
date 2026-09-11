<?php

namespace App\Admin\Http\Requests\Transaction;

class RejectWithdrawRequest extends BaseWithdrawActionRequest
{
    /**
     * Các quy tắc validation khi từ chối lệnh rút tiền
     */
    protected function methodPost(): array
    {
        return [
            'reason' => ['required', 'string', 'max:500'],
        ];
    }

    /**
     * Thông báo lỗi validation tiếng Việt
     */
    public function messages(): array
    {
        return [
            'reason.required' => __('Vui lòng nhập lý do từ chối để gửi thông báo cho đối tác.'),
            'reason.string' => __('Lý do từ chối phải là dạng chuỗi văn bản.'),
            'reason.max' => __('Lý do từ chối không được vượt quá :max ký tự.', ['max' => 500]),
        ];
    }
}
