<?php

namespace App\Api\V1\Repositories\Child;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface ChildRepositoryInterface extends EloquentRepositoryInterface
{
    public function exists($id): bool;
}
