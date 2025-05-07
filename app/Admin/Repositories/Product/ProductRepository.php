<?php

namespace App\Admin\Repositories\Product;

use App\Admin\Repositories\EloquentRepository;
use App\Enums\ActiveStatus;
use App\Enums\Product\ProductStatus;
use App\Enums\Brand\BrandStatus;
use App\Models\Product;
use App\Models\Brand;
use App\Models\ProductCatalog;


class ProductRepository extends EloquentRepository implements ProductRepositoryInterface
{
    public function getModel(): string
    {
        return Product::class;
    }
    public function getAllBrands()
    {
        return Brand::where('status', BrandStatus::Active->value)->get();
    }
    public function searchAllLimit(string $keySearch = '', array $meta = [], int $limit = 10)
    {

        $query = $this->model->where('status', '=', ProductStatus::Active);


        if (!empty($keySearch)) {
            $query->where('name', 'like', '%' . $keySearch . '%');
        }


        if (!empty($meta)) {
            $this->applyFilters($meta, $query);
        }


        return $query->limit($limit)->get();
    }
    public function findOrFailWithRelations($id, array $relations = ['productCatalogs'])
    {
        $this->findOrFail($id);
        $this->instance = $this->instance->load($relations);
        return $this->instance;
    }
    public function syncProductCatalogs(Product $product, array $productCatalogId)
    {
        return $product->productCatalogs()->sync($productCatalogId);
    }
    public function getAllProductCatalogs()
    {
        return ProductCatalog::where('status', ActiveStatus::Active)->get();
    }
    public static function asSelectArray(): array
    {
        return self ::pluck('name', 'id')->toArray();
    }
    public function getAllBrandsAsSelectArray(): array
    {
        return Brand::pluck('name', 'id')->toArray();
    }

    public function getAllProductCatalogAsSelectArray(): array
    {
        return ProductCatalog::pluck('name', 'id')->toArray();
    }

}
