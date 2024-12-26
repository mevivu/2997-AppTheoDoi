<?php

namespace App\Admin\Repositories\SubjectGrade;

use App\Admin\Repositories\EloquentRepository;

use App\Models\SubjectGrade;

class SubjectGradeRepository extends EloquentRepository implements SubjectGradeRepositoryInterface
{
    public function getModel(): string
    {
        return SubjectGrade::class;
    }



}
