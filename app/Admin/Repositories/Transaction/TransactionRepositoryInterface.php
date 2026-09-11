<?php

namespace App\Admin\Repositories\Transaction;

use App\Admin\Repositories\EloquentRepositoryInterface;
use App\Models\Transaction;

interface TransactionRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Tạo bản ghi giao dịch rút tiền hoa hồng
     *
     * @param array $data Thông tin giao dịch rút tiền
     * @return Transaction Đối tượng giao dịch vừa tạo
     */
    public function createWithdrawTransaction(array $data): Transaction;

    /**
     * Tìm giao dịch và khóa dòng dữ liệu chống race condition
     *
     * @param int $id
     * @return Transaction|null
     */
    public function findForUpdate(int $id): ?Transaction;
}
