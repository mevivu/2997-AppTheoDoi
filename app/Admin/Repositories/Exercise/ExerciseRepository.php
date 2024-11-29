<?php

namespace App\Admin\Repositories\Exercise;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Exercise;

class ExerciseRepository extends EloquentRepository implements ExerciseRepositoryInterface
{
    public function getModel()
    {
        return Exercise::class;
    }
}