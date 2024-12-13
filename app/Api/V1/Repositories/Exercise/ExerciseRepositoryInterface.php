<?php

namespace App\Api\V1\Repositories\Exercise;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface ExerciseRepositoryInterface extends EloquentRepositoryInterface
{
    public function index(int $limit = 10, int $page = 1);
}
