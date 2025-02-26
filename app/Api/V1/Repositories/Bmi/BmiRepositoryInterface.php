<?php

namespace App\Api\V1\Repositories\Bmi;
use App\Admin\Repositories\EloquentRepositoryInterface;

interface BmiRepositoryInterface extends EloquentRepositoryInterface
{

    public function index($limit = 10, $page = 1);
}
