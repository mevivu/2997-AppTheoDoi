<?php

namespace App\Api\V1\Repositories\Classes;

use \App\Admin\Repositories\Classes\ClassesRepository as AdminArea;
use App\Enums\ActiveStatus;

class ClassesRepository extends AdminArea implements ClassesRepositoryInterface
{
    protected $model;

    public function findClassesActive()
    {
        return $this->model->where('status', ActiveStatus::Active)->get();
    }

    public function findSubjectsByClasses($classId)
    {
        // TODO: Implement findSubjectsByClasses() method.
        $class = $this->model->find($classId);
        if (!$class) {
            throw new \Exception("Class not found with ID: $classId");
        }
        return $class->subjects()->get();
    }
}
