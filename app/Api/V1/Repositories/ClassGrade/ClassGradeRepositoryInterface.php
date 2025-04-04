<?php

namespace App\Api\V1\Repositories\ClassGrade;


use App\Admin\Repositories\EloquentRepositoryInterface;

interface ClassGradeRepositoryInterface extends EloquentRepositoryInterface
{
    public function hasGradesGreaterThanZero($childId);
}
