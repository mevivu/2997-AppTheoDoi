<?php

namespace App\Admin\Repositories\Classes;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Classes;

class ClassesRepository extends EloquentRepository implements ClassesRepositoryInterface
{

    protected $select = [];

    public function getModel(): string
    {
        return Classes::class;
    }

    public function searchAllLimit($keySearch = '', $meta = [], $select = ['id', 'name'], $limit = 10)
    {
        $this->instance = $this->model->select($select);
        $this->getQueryBuilderFindByKey($keySearch);

        foreach ($meta as $key => $value) {
            $this->instance = $this->instance->where($key, $value);
        }

        return $this->instance->limit($limit)->get();
    }

    protected function getQueryBuilderFindByKey($key): void
    {
        $this->instance = $this->instance->where(function ($query) use ($key) {
            return $query->where('name', 'LIKE', '%' . $key . '%');
        });
    }

}