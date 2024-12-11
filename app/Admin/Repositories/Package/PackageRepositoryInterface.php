<?php

namespace App\Admin\Repositories\Package;
use App\Admin\Repositories\EloquentRepositoryInterface;

interface PackageRepositoryInterface extends EloquentRepositoryInterface
{
    public function searchAllLimit($keySearch = '', $meta = [], $limit = 10);

}
