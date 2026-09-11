<?php

namespace App\Admin\Repositories\Transaction;

use App\Admin\Repositories\EloquentRepository;
use App\Enums\Transaction\TransactionEnumService;
use App\Enums\Transaction\TransactionStatus;
use App\Enums\Transaction\TransactionType;
use App\Models\Transaction;

class TransactionRepository extends EloquentRepository implements TransactionRepositoryInterface
{
    public function getModel(): string
    {
        return Transaction::class;
    }

    /**
     * Tạo bản ghi giao dịch rút tiền hoa hồng
     *
     * @param array $data Thông tin giao dịch rút tiền
     * @return Transaction Đối tượng giao dịch vừa tạo
     */
    public function createWithdrawTransaction(array $data): Transaction
    {
        return $this->model->create([
            'code' => $data['code'],
            'user_id' => $data['user_id'],
            'package_id' => null,
            'amount' => $data['amount'],
            'type' => TransactionType::Withdraw,
            'status' => TransactionStatus::Pending,
            'service' => TransactionEnumService::NORMAL,
            'bank_name' => trim($data['bank_name']),
            'bank_account_number' => trim($data['bank_account_number']),
            'bank_account_name' => strtoupper(trim($data['bank_account_name'])),
            'scheduled_payout_date' => $data['scheduled_payout_date'] ?? null,
            'admin_note' => $data['admin_note'] ?? ($data['user_note'] ?? null),
        ]);
    }
}
