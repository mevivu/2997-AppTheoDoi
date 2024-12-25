<?php

namespace App\Admin\Repositories\Classes;

use App\Admin\Repositories\EloquentRepository;
use App\Enums\ActiveStatus;
use App\Models\Classes;
use App\Models\SchoolClass;

class ClassesRepository extends EloquentRepository implements ClassesRepositoryInterface
{

    protected $select = [];

    public function getModel(): string
    {
        return SchoolClass::class;
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


    public function getClassSubject($id)
    {
        // TODO: Implement getClassSubject() method.
        return $this->model->select(['classes.id as id','classes.status', 'classes.name as name','class_subject.subject_id as subject_id',
            'subjects.name as nameSubject'])->join('class_subject', 'classes.id', '=', 'class_subject.class_id')
            ->join('subjects','subjects.id','=','class_subject.subject_id')->where('classes.id', $id)->first();
    }
}
