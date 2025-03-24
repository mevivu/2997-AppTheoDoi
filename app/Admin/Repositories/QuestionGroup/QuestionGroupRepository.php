<?php

namespace App\Admin\Repositories\QuestionGroup;

use App\Admin\Repositories\EloquentRepository;
use App\Models\QuestionGroup;

class QuestionGroupRepository extends EloquentRepository implements QuestionGroupRepositoryInterface
{
    public function getModel()
    {
        return QuestionGroup::class;
    }

    public function getByActiveAndTypes($status, $types)
    {
        return QuestionGroup::where('status', $status)
            ->whereIn('type', $types)
            ->get();
    }
}
