<?php

namespace App\Admin\Repositories\Introduction;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface IntroductionRepositoryInterface extends EloquentRepositoryInterface
{
    public function getQueryBuilderOrderBy($column = 'sort_order', $sort = 'ASC');
}
