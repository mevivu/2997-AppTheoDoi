<?php

namespace App\Admin\Repositories\Journal;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Journal;

class JournalRepository extends EloquentRepository implements JournalRepositoryInterface
{
    public function getModel()
    {
        return Journal::class;
    }
}
