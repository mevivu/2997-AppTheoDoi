<?php

namespace App\Admin\Repositories\Clinic;
use App\Admin\Repositories\EloquentRepositoryInterface;

interface ClinicRepositoryInterface extends EloquentRepositoryInterface
{
    public function searchAllLimit($keySearch = '', $meta = [], $limit = 10);

}
