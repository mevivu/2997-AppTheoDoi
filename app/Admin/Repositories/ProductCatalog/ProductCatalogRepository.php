<?php

namespace App\Admin\Repositories\ProductCatalog;

use App\Admin\Repositories\EloquentRepository;
use App\Models\ProductCatalog;

class ProductCatalogRepository extends EloquentRepository implements ProductCatalogRepositoryInterface
{
    public function getModel(): string
    {
        return ProductCatalog::class;
    }
}
