<?php

namespace App\Admin\Repositories\ExerciseMedia;

use App\Admin\Repositories\EloquentRepository;
use App\Models\ExerciseMedia;

class ExerciseMediaRepository extends EloquentRepository implements ExerciseMediaRepositoryInterface
{
    public function getModel()
    {
        return ExerciseMedia::class;
    }
}
