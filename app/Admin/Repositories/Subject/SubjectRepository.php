<?php

namespace App\Admin\Repositories\Subject;

use App\Admin\Repositories\EloquentRepository;

use App\Models\Subject;

class SubjectRepository extends EloquentRepository implements SubjectRepositoryInterface
{
    public function getModel(): string
    {
        return Subject::class;
    }
}
