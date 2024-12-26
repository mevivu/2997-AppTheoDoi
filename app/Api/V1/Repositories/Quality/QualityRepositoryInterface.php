<?php

namespace App\Api\V1\Repositories\Quality;


use App\Admin\Repositories\EloquentRepositoryInterface;

interface QualityRepositoryInterface extends EloquentRepositoryInterface
{

    public function index();
}
