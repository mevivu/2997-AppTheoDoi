<?php

namespace App\Api\V1\Repositories\Classes;

use \App\Admin\Repositories\Classes\ClassesRepository as AdminArea;
use App\Enums\ActiveStatus;
use App\Models\SchoolClass;

class ClassesRepository extends AdminArea implements ClassesRepositoryInterface
{
    protected $model;

    public function __construct(SchoolClass $note)
    {
        $this->model = $note;
    }

    public function findClassesActive()
    {
        // TODO: Implement findClassesActive() method.
        return $this->model->where('status', ActiveStatus::Active)->get();
    }
}
