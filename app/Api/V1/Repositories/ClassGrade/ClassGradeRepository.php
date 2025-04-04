<?php

namespace App\Api\V1\Repositories\ClassGrade;

use App\Admin\Repositories\ClassGrade\ClassGradeRepository as AdminArea;
use App\Models\ClassGrade;

class ClassGradeRepository extends AdminArea implements ClassGradeRepositoryInterface
{

    public function hasGradesGreaterThanZero($childId)
    {
        return ClassGrade::where('child_id', $childId)
            ->where(function ($query) {
                $query->where('semester1_grade', '>', 0)
                    ->orWhere('semester2_grade', '>', 0)
                    ->orWhere('full_year_grade', '>', 0);
            })
            ->exists();
    }
}
