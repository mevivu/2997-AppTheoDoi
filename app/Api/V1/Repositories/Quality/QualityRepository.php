<?php

namespace App\Api\V1\Repositories\Quality;

use App\Admin\Repositories\Quality\QualityRepository as AdminArea;
use App\Enums\ActiveStatus;

class QualityRepository extends AdminArea implements QualityRepositoryInterface
{
    protected $model;


    public function index()
    {
        return $this->model->where('status', ActiveStatus::Active)->get();
    }
}
