<?php

namespace App\Admin\Repositories\WeightHeightWho;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Repositories\Ward\WardRepositoryInterface;
use App\Models\District;
use App\Models\Ward;
use App\Models\WeightHeightWHO;

class WeightHeightWhoRepository extends EloquentRepository implements WeightHeightWhoRepositoryInterface
{

    protected $select = [];


    public function getModel()
    {
        // TODO: Implement getModel() method.
        return WeightHeightWHO::class;
    }
}
