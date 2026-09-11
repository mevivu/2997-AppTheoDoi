<?php

namespace App\Admin\Repositories\Bank;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface BankRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Lấy danh sách ngân hàng (hỗ trợ tìm kiếm theo keyword)
     *
     * @param string|null $keyword
     * @return mixed
     */
    public function getAllBanks(?string $keyword = null);
}
