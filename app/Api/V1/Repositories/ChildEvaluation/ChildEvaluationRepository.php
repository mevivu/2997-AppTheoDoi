<?php

namespace App\Api\V1\Repositories\ChildEvaluation;

use App\Admin\Repositories\ChildEvaluation\ChildEvaluationRepository as AdminRepository;
use Illuminate\Database\Eloquent\Builder;

class ChildEvaluationRepository extends AdminRepository implements ChildEvaluationRepositoryInterface
{
    public function buildQuery($criteria): Builder
    {
        $query = $this->model->newQuery();

        if (!empty($criteria['class_grade_id'])) {
            $query->where('class_grade_id', $criteria['class_grade_id']);
        }

        if (!empty($criteria['class_id'])) {
            $query->whereHas('classGrade', function ($query) use ($criteria) {
                $query->where('class_id', $criteria['class_id']);
            });
        }

        if (!empty($criteria['semester'])) {
            $query->where('semester', $criteria['semester']);
        }

        return $query;
    }

}
