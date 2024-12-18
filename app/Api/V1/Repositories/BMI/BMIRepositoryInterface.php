<?php

namespace App\Api\V1\Repositories\BMI;


use App\Admin\Repositories\EloquentRepositoryInterface;

interface BMIRepositoryInterface extends EloquentRepositoryInterface
{

    public function index($limit = 10, $page = 1);
}
