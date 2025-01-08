<?php

namespace App\Admin\Repositories\Brand;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Traits\Roles;
use App\Enums\Brand\BrandStatus;
use App\Models\Brand;

class BrandRepository extends EloquentRepository implements BrandRepositoryInterface
{
    use Roles;

    protected $select = [];

    public function getModel(): string
    {
        return Brand::class;
    }

    public function searchAllLimit(string $keySearch = '', array $meta = [], int $limit = 10)
    {
        // Khởi tạo query với điều kiện mặc định
        $query = $this->model->where('status', '=', BrandStatus::Active);

        // Kiểm tra và áp dụng tìm kiếm theo tên nếu có
        if (!empty($keySearch)) {
            $query->where('name', 'like', '%' . $keySearch . '%');
        }

        // Áp dụng các filter tùy chỉnh nếu có
        if (!empty($meta)) {
            $this->applyFilters($meta, $query);
        }

        // Thực hiện truy vấn với giới hạn
        return $query->limit($limit)->get();
    }
}
