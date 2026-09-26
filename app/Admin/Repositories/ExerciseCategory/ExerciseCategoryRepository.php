<?php

namespace App\Admin\Repositories\ExerciseCategory;

use App\Admin\Repositories\EloquentRepository;
use App\Models\ExerciseCategory;

class ExerciseCategoryRepository extends EloquentRepository implements ExerciseCategoryRepositoryInterface
{
    public function getModel()
    {
        return ExerciseCategory::class;
    }
}
