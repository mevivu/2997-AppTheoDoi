<?php

namespace App\Api\V1\Services\Product;

use Illuminate\Pagination\LengthAwarePaginator;

interface ProductServiceInterface
{
    public function getProducts($data): LengthAwarePaginator;
}
