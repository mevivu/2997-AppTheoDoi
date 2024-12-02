<?php

namespace App\Admin\Repositories\ClinicType;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Traits\BaseAuthCMS;
use App\Admin\Traits\Roles;
use App\Enums\ActiveStatus;
use App\Models\ClinicType;

class ClinicTypeRepository extends EloquentRepository implements ClinicTypeRepositoryInterface
{
    use Roles;
    use BaseAuthCMS;

    protected $select = [];

    public function getModel(): string
    {
        return ClinicType::class;
    }
    public function getAll()
    {
        return $this->model->all();
    }

    public function searchAllLimit($keySearch = '', $meta = [], $limit = 10)
    {
        $this->instance = $this->model->where('status', '=', ActiveStatus::Active)
            ->where('name', 'like', '%' . $keySearch . '%');

        $this->applyFilters($meta);
        return $this->instance->limit($limit)->get();
    }

}
