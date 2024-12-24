<?php

namespace App\Admin\Repositories\Capability;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Capability;

class CapabilityRepository extends EloquentRepository implements CapabilityRepositoryInterface
{
    public function getModel(): string
    {
        return Capability::class;
    }
}
