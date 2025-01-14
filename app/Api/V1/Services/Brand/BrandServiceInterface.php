<?php

namespace App\Api\V1\Services\Brand;

use Illuminate\Pagination\LengthAwarePaginator;

interface BrandServiceInterface
{
    public function getBrands($data): LengthAwarePaginator;
}
