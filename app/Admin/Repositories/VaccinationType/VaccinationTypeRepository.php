<?php

namespace App\Admin\Repositories\VaccinationType;

use App\Admin\Repositories\EloquentRepository;
use App\Models\VaccinationType;

class VaccinationTypeRepository extends EloquentRepository implements VaccinationTypeRepositoryInterface
{
    public function getModel(): string
    {
        return VaccinationType::class;
    }

    public function searchAllLimit($keySearch = '', $meta = [], $select = ['id', 'name'], $limit = 12)
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
