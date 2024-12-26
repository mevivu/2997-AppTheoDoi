<?php

namespace App\Admin\Repositories\ChildCapability;

use App\Admin\Repositories\EloquentRepository;
use App\Models\ChildCapability;

class ChildCapabilityRepository extends EloquentRepository implements ChildCapabilityRepositoryInterface
{
    public function getModel(): string
    {
        return ChildCapability::class;
    }
}
