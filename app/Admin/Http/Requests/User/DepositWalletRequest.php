<?php

namespace App\Admin\Http\Requests\User;

use App\Admin\Http\Requests\BaseRequest;

class DepositWalletRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('amount')) {
            // Loại bỏ các ký tự phân cách như dấu chấm, phẩy, khoảng trắng nếu gửi lên dạng formatted
            $raw = (string) $this->amount;
            $clean = str_replace(['.', ',', ' ', 'đ', 'VND', 'vnd'], '', $raw);
            $this->merge([
                'amount' => is_numeric($clean) ? (float) $clean : $this->amount,
            ]);
        }

        if ($this->has('send_notification')) {
            $this->merge([
                'send_notification' => filter_var($this->send_notification, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }

    protected function methodPost(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'min:1000', 'max:500000000'],
            'admin_note' => ['required', 'string', 'min:3', 'max:255'],
            'send_notification' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Vui lòng chọn tài khoản thành viên.',
            'user_id.exists' => 'Tài khoản thành viên không tồn tại trên hệ thống.',
            'amount.required' => 'Vui lòng nhập số tiền cần nạp.',
            'amount.numeric' => 'Số tiền nạp phải là dạng số hợp lệ.',
            'amount.min' => 'Số tiền nạp tối thiểu là 1.000đ.',
            'amount.max' => 'Số tiền nạp một lần không được vượt quá 500.000.000đ.',
            'admin_note.required' => 'Vui lòng nhập lý do nạp tiền vào ví.',
            'admin_note.min' => 'Lý do nạp tiền phải có ít nhất 3 ký tự.',
            'admin_note.max' => 'Lý do nạp tiền không được vượt quá 255 ký tự.',
        ];
    }
}
