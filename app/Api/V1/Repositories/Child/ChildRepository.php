<?php

namespace App\Api\V1\Repositories\Child;

use App\Admin\Repositories\Children\ChildrenRepository as AdminRepository;
use App\Models\Child;


class ChildRepository extends AdminRepository implements ChildRepositoryInterface
{
    public function exists(int $id): bool
    {
        return Child::where('id', $id)->exists();
    }
}
