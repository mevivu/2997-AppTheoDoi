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


}
