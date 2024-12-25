<?php

namespace App\Admin\Repositories\Subject;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface SubjectRepositoryInterface extends EloquentRepositoryInterface
{
    //
    public function searchAllLimit($keySearch = '', $meta = [], $limit = 10);
}
