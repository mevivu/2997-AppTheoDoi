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
        $query = $this->model->where('status', '=', BrandStatus::Active);

        if (!empty($keySearch)) {
            $query->where('name', 'like', '%' . $keySearch . '%');
        }

        if (!empty($meta)) {
            $this->applyFilters($meta, $query);
        }
        return $query->limit($limit)->get();
    }
}
