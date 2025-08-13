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
                $query->whereNotNull('semester1_grade')
                    ->orWhereNotNull('semester2_grade')
                    ->orWhereNotNull('full_year_grade');
            })
            ->exists();
    }

}
