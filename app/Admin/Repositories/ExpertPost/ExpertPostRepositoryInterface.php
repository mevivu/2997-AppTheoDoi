<?php

namespace App\Admin\Repositories\ExpertPost;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface ExpertPostRepositoryInterface extends EloquentRepositoryInterface
{
    public function findOrFailWithRelations($id, array $relations = ['expert', 'category', 'ageGroup']);
    public function getQueryBuilderOrderBy($column = 'id', $sort = 'DESC');
}
