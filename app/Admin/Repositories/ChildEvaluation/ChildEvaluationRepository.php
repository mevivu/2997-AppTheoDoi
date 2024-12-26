<?php

namespace App\Admin\Repositories\ChildEvaluation;

use App\Admin\Repositories\EloquentRepository;
use App\Models\ChildEvaluation;

class ChildEvaluationRepository extends EloquentRepository implements ChildEvaluationRepositoryInterface
{
    public function getModel(): string
    {
        return ChildEvaluation::class;
    }
}
