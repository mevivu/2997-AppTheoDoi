<?php

namespace App\Admin\Repositories\Children;
use App\Admin\Repositories\EloquentRepositoryInterface;

interface ChildrenRepositoryInterface extends EloquentRepositoryInterface
{
    public function searchAllLimit($value = '', $meta = [], $select = [], $limit = 10, $role);
    public function getQueryBuilderOrderBy($column = 'id', $sort = 'DESC');
    public function findMany(array $ids);
}
