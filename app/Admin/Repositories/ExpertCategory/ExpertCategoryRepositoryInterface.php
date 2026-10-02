<?php

namespace App\Admin\Repositories\ExpertCategory;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface ExpertCategoryRepositoryInterface extends EloquentRepositoryInterface
{
    public function getQueryBuilderOrderBy($column = 'sort_order', $sort = 'ASC');
}
