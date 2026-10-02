<?php

namespace App\Admin\Repositories\ExpertCategory;

use App\Admin\Repositories\EloquentRepository;
use App\Models\ExpertCategory;

class ExpertCategoryRepository extends EloquentRepository implements ExpertCategoryRepositoryInterface
{
    public function getModel(): string
    {
        return ExpertCategory::class;
    }

    public function getQueryBuilderOrderBy($column = 'sort_order', $sort = 'ASC')
    {
        $this->getQueryBuilder();
        $this->instance = $this->instance->orderBy($column, $sort);
        return $this->instance;
    }
}
