<?php

namespace App\Admin\Repositories\District;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Repositories\District\DistrictRepositoryInterface;
use App\Models\District;
use App\Models\Province;

class DistrictRepository extends EloquentRepository implements DistrictRepositoryInterface
{

    protected $select = [];

    public function getModel(): string
    {
        return District::class;
    }

    public function searchAllLimit($keySearch = '', $provinceId = 0)
    {
        $province = Province::find($provinceId);
        $this->instance = $this->model->where('province_code', $province->code)->where('name', 'like', "%{$keySearch}%");
        return $this->instance->get();
    }
}
