<?php

namespace App\Admin\Repositories\Bank;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Bank;

class BankRepository extends EloquentRepository implements BankRepositoryInterface
{
    protected $select = [];

    public function getModel(): string
    {
        return Bank::class;
    }

    /**
     * Lấy danh sách ngân hàng (hỗ trợ tìm kiếm theo keyword)
     *
     * @param string|null $keyword
     * @return mixed
     */
    public function getAllBanks(?string $keyword = null)
    {
        $query = $this->model->newQuery();

        if (!empty($keyword)) {
            $keyword = trim($keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('shortName', 'like', "%{$keyword}%")
                  ->orWhere('name', 'like', "%{$keyword}%")
                  ->orWhere('code', 'like', "%{$keyword}%")
                  ->orWhere('bin', 'like', "%{$keyword}%");
            });
        }

        return $query->orderBy('id', 'asc')->get();
    }
}
