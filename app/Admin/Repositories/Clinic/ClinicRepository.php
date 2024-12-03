<?php

namespace App\Admin\Repositories\Clinic;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Traits\BaseAuthCMS;
use App\Admin\Traits\Roles;
use App\Enums\ActiveStatus;
use App\Models\Clinic;

class ClinicRepository extends EloquentRepository implements ClinicRepositoryInterface
{
    use Roles;
    use BaseAuthCMS;

    protected $select = [];

    public function getModel(): string
    {
        return Clinic::class;
    }

    public function searchAllLimit($keySearch = '', $meta = [], $limit = 10)
    {
        $this->instance = $this->model->where('status', '=', ActiveStatus::Active)
            ->where('name', 'like', '%' . $keySearch . '%');

        $this->applyFilters($meta);
        return $this->instance->limit($limit)->get();
    }

}
