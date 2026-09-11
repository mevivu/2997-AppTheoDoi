<?php

namespace App\Admin\Http\Requests\Transaction;

class ApproveWithdrawRequest extends BaseWithdrawActionRequest
{
    /**
     * Các quy tắc validation khi duyệt chi trả
     */
    protected function methodPost(): array
    {
        return [
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Thông báo lỗi validation tiếng Việt
     */
    public function messages(): array
    {
        return [
            'note.string' => __('Ghi chú/Mã tham chiếu phải là dạng chuỗi ký tự.'),
            'note.max' => __('Ghi chú không được vượt quá :max ký tự.', ['max' => 500]),
        ];
    }
}
