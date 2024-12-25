<?php

namespace App\Admin\Repositories\Classes;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface ClassesRepositoryInterface extends EloquentRepositoryInterface
{
    public function searchAllLimit($value = '', $meta = [], $select = [], $limit = 12);
    public function getClassSubject($id);

}
