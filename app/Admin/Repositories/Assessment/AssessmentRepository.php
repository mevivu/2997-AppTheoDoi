<?php

namespace App\Admin\Repositories\Assessment;

use App\Admin\Repositories\Answer\AnswerRepositoryInterface;
use App\Admin\Repositories\EloquentRepository;
use App\Models\Assessment;

class AssessmentRepository extends EloquentRepository implements AnswerRepositoryInterface
{
    public function getModel(): string
    {
        return Assessment::class;
    }
}
