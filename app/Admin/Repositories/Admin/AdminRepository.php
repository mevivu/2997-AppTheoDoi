<?php

namespace App\Admin\Repositories\Admin;
use App\Admin\Repositories\EloquentRepository;
use App\Admin\Repositories\Admin\AdminRepositoryInterface;
use App\Admin\Traits\BaseAuthCMS;
use App\Models\Admin;

class AdminRepository extends EloquentRepository implements AdminRepositoryInterface
{
    use BaseAuthCMS;

    protected $select = [];

    public function getModel(): string
    {
        return Admin::class;
    }

    public function getQueryBuilderOrderBy($column = 'id', $sort = 'DESC'){
        $this->getQueryBuilder();
        $this->instance = $this->instance->with('roles')->orderBy($column, $sort);
        return $this->instance;
    }



}
