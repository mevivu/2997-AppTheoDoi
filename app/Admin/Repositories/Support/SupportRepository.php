<?php

namespace App\Admin\Repositories\Support;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Support;

class SupportRepository extends EloquentRepository implements SupportRepositoryInterface
{
    public function getModel()
    {
        return Support::class;
    }
}