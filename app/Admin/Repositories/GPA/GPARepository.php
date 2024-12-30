<?php

namespace App\Admin\Repositories\GPA;

use App\Admin\Repositories\EloquentRepository;
use App\Models\ClassGrade;

class GPARepository extends EloquentRepository implements GPARepositoryInterface
{
    public function getModel()
    {
        return ClassGrade::class;
    }
}
