<?php

namespace App\Api\V1\Repositories\Capability;


use App\Admin\Repositories\EloquentRepositoryInterface;

interface CapabilityRepositoryInterface extends EloquentRepositoryInterface
{
    public function getAllCapabilities();
}
