<?php

namespace App\Api\V1\Repositories\Classes;


use App\Admin\Repositories\EloquentRepositoryInterface;

interface ClassesRepositoryInterface extends EloquentRepositoryInterface
{
    public function findClassesActive();

    public function findSubjectsByClasses($classId);
}
