<?php

namespace App\Admin\Repositories\Introduction;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Introduction;

class IntroductionRepository extends EloquentRepository implements IntroductionRepositoryInterface
{
    public function getModel(): string
    {
        return Introduction::class;
    }

    public function getQueryBuilderOrderBy($column = 'sort_order', $sort = 'ASC')
    {
        $this->getQueryBuilder();
        $this->instance = $this->instance->orderBy($column, $sort);
        return $this->instance;
    }
}
