<?php

namespace App\Admin\Repositories\Journal;

use App\Admin\Repositories\EloquentRepository;
use App\Models\JournalMilestone;

class JournalMilestoneRepository extends EloquentRepository implements JournalMilestoneRepositoryInterface
{
    public function getModel()
    {
        return JournalMilestone::class;
    }
}
