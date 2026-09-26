<?php

namespace App\Admin\Repositories\AgeGroup;

use App\Admin\Repositories\EloquentRepository;
use App\Models\AgeGroup;

class AgeGroupRepository extends EloquentRepository implements AgeGroupRepositoryInterface
{
    public function getModel()
    {
        return AgeGroup::class;
    }
}
