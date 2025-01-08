<?php

namespace App\Admin\Repositories\Product;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface ProductRepositoryInterface extends EloquentRepositoryInterface
{
    public function searchAllLimit(string $keySearch = '',array $meta = [],int $limit = 10);
    public function getAllBrands();
    public function findOrFailWithRelations(int $id, array $relations = ['productCatalogs']);
    public function attachProductCatalogs(\App\Models\Product $product, array $productCatalogId);
    public function syncProductCatalogs(\App\Models\Product $product, array $productCatalogId);
    public function getAllProductCatalogs();


}
