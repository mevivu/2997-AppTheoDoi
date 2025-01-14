<?php

namespace App\Api\V1\Services\ProductCatalog;

use Illuminate\Pagination\LengthAwarePaginator;

interface ProductCatalogServiceInterface
{
    public function getProductCatalogs($data): LengthAwarePaginator;
}
