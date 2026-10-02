<?php

namespace App\Admin\Repositories\Expert;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Expert;

class ExpertRepository extends EloquentRepository implements ExpertRepositoryInterface
{
    public function getModel(): string
    {
        return Expert::class;
    }

    public function getQueryBuilderOrderBy($column = 'sort_order', $sort = 'ASC')
    {
        $this->getQueryBuilder();
        $this->instance = $this->instance->orderBy($column, $sort);
        return $this->instance;
    }
}
