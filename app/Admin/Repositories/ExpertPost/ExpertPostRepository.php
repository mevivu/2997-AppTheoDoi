<?php

namespace App\Admin\Repositories\ExpertPost;

use App\Admin\Repositories\EloquentRepository;
use App\Models\ExpertPost;

class ExpertPostRepository extends EloquentRepository implements ExpertPostRepositoryInterface
{
    public function getModel(): string
    {
        return ExpertPost::class;
    }

    public function findOrFailWithRelations($id, array $relations = ['expert', 'category', 'ageGroup'])
    {
        $this->findOrFail($id);
        $this->instance = $this->instance->load($relations);
        return $this->instance;
    }

    public function getQueryBuilderOrderBy($column = 'id', $sort = 'DESC')
    {
        $this->getQueryBuilder();
        $this->instance = $this->instance->orderBy($column, $sort);
        return $this->instance;
    }
}
