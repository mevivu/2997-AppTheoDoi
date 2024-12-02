<?php

namespace App\Admin\Repositories\ClinicType;
use App\Admin\Repositories\EloquentRepositoryInterface;

interface ClinicTypeRepositoryInterface extends EloquentRepositoryInterface
{
    public function searchAllLimit($keySearch = '', $meta = [], $limit = 10);

}
