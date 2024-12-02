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


}
