<?php

namespace App\Api\V1\Repositories\Capability;

use App\Admin\Repositories\Capability\CapabilityRepository as AdminArea;
use App\Enums\ActiveStatus;

class CapabilityRepository extends AdminArea implements CapabilityRepositoryInterface
{
    protected $model;


    public function getAllCapabilities()
    {
        return $this->model->where('status', ActiveStatus::Active)->get();
    }
}
