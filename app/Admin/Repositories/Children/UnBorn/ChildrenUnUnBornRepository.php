<?php

namespace App\Admin\Repositories\Children\UnBorn;

use App\Admin\Repositories\EloquentRepository;
use App\Enums\Child\BornStatus;
use App\Models\Child;

class ChildrenUnUnBornRepository extends EloquentRepository implements ChildrenUnBornRepositoryInterface
{

    protected $select = [];

    public function getModel(): string
    {
        return Child::class;
    }

    public function searchAllLimit($keySearch = '', $meta = [], $select = ['id', 'fullname', 'gender'], $limit = 10, $role = 0)
    {
        $this->instance = $this->model->select($select);
        $this->instance = $this->instance->where('is_born', BornStatus::Unborn);

        $this->getQueryBuilderFindByKey($keySearch);

        foreach ($meta as $key => $value) {
            $this->instance = $this->instance->where($key, $value);
        }

        return $this->instance->limit($limit)->get();
    }

    protected function getQueryBuilderFindByKey($key): void
    {
        $this->instance = $this->instance->where(function ($query) use ($key) {
            return $query->where('fullname', 'LIKE', '%' . $key . '%')
                ->orWhere('gender', 'LIKE', '%' . $key . '%');

        });
    }

    public function getQueryBuilderOrderBy($column = 'id', $sort = 'DESC')
    {
        $this->getQueryBuilder();
        $this->instance = $this->instance->orderBy($column, $sort);
        return $this->instance;
    }



}
