<?php

namespace App\Admin\Repositories\Province;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Repositories\Province\ProvinceRepositoryInterface;
use App\Models\Province;

class ProvinceRepository extends EloquentRepository implements ProvinceRepositoryInterface
{

    protected $select = [];

    public function getModel(): string
    {
        return Province::class;
    }

    public function searchAllLimit($keySearch = '', $meta = [], $limit = 10){

        $this->instance = $this->model->where('name', 'like', "%{$keySearch}%");

        foreach($meta as $key => $value){
            $this->instance = $this->instance->where($key, $value);
        }
        return $this->instance->get();
    }


}
