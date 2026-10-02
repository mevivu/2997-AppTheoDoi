<?php

namespace App\Admin\Repositories\Expert;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface ExpertRepositoryInterface extends EloquentRepositoryInterface
{
    public function getQueryBuilderOrderBy($column = 'sort_order', $sort = 'ASC');
}
