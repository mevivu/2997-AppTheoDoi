<?php

namespace App\Admin\Repositories\Package;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Traits\Roles;
use App\Enums\ActiveStatus;
use App\Models\Package;

class PackageRepository extends EloquentRepository implements PackageRepositoryInterface
{
    use Roles;

    protected $select = [];

    public function getModel(): string
    {
        return Package::class;
    }

    public function searchAllLimit($keySearch = '', $meta = [], $limit = 10)
    {
        $this->instance = $this->model->where('status', '=', ActiveStatus::Active)
            ->where('name', 'like', '%' . $keySearch . '%');

        $this->applyFilters($meta);
        return $this->instance->limit($limit)->get();
    }

}
