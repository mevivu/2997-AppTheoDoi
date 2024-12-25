<?php

namespace App\Admin\Repositories\ClassGrade;

use App\Admin\Repositories\EloquentRepository;
use App\Models\ClassGrade;

class ClassGradeRepository extends EloquentRepository implements ClassGradeRepositoryInterface
{

    public function getModel(): string
    {
        return ClassGrade::class;
    }



}
