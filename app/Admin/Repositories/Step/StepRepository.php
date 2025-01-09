<?php

namespace App\Admin\Repositories\Step;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Step;

class StepRepository extends EloquentRepository implements StepRepositoryInterface
{

    protected $select = [];

    public function getModel(): string
    {
        return Step::class;
    }


}
