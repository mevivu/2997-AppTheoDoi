<?php

namespace App\Admin\Repositories\Brand;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface BrandRepositoryInterface extends EloquentRepositoryInterface
{
    public function searchAllLimit(string $keySearch = '',array $meta = [],int $limit = 10);

}
