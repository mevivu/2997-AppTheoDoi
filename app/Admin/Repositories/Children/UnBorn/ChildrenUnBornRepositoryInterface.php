<?php

namespace App\Admin\Repositories\Children\UnBorn;
use App\Admin\Repositories\EloquentRepositoryInterface;

interface ChildrenUnBornRepositoryInterface extends EloquentRepositoryInterface
{
    public function searchAllLimit($value = '', $meta = [], $select = [], $limit = 10, $role);

    public function getQueryBuilderOrderBy($column = 'id', $sort = 'DESC');
}
