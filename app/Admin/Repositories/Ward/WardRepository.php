<?php

namespace App\Admin\Repositories\Ward;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Ward;

class WardRepository extends EloquentRepository implements WardRepositoryInterface
{

    protected $select = [];

    public function getModel(): string
    {
        return Ward::class;
    }


}
