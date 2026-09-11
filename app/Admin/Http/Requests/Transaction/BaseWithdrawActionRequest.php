<?php

namespace App\Admin\Http\Requests\Transaction;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\Transaction\TransactionStatus;
use App\Enums\Transaction\TransactionType;
use App\Models\Transaction;

abstract class BaseWithdrawActionRequest extends BaseRequest
{
    /**
     * Xác định xem user có quyền thực hiện request này không
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Chuẩn bị dữ liệu trước khi validate (gắn id từ route vào data)
     */
    protected function prepareForValidation(): void
    {
        if ($this->route('id')) {
            $this->merge([
                'id' => $this->route('id'),
            ]);
        }
    }

    /**
     * Kiểm tra tính hợp lệ của giao dịch rút tiền
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $transactionId = $this->route('id') ?: $this->input('id');
            $transaction = Transaction::find($transactionId);

            if (!$transaction) {
                $validator->errors()->add('id', __('Không tìm thấy giao dịch.'));
                return;
            }

            if ($transaction->type !== TransactionType::Withdraw) {
                $validator->errors()->add('id', __('Giao dịch này không phải là lệnh rút tiền.'));
                return;
            }

            if ($transaction->status !== TransactionStatus::Pending) {
                $validator->errors()->add('id', __('Giao dịch này đã được xử lý trước đó (Trạng thái: :status).', [
                    'status' => $transaction->status->label(),
                ]));
            }
        });
    }

    /**
     * Lấy transaction hợp lệ đã được kiểm tra
     */
    public function getValidatedTransaction(): ?Transaction
    {
        $transactionId = $this->route('id') ?: $this->input('id');
        return Transaction::find($transactionId);
    }
}
