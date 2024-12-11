<?php

namespace App\Admin\Repositories\Expected;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Expected;

class ExpectedRepository extends EloquentRepository implements ExpectedRepositoryInterface
{
    public function getModel()
    {
        return Expected::class;
    }
}