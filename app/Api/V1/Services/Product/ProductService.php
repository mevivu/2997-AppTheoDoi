<?php

namespace App\Api\V1\Services\Product;

use App\Enums\ActiveStatus;
use App\Api\V1\Repositories\Product\ProductRepositoryInterface;
use App\Api\V1\Http\Resources\Product\ProductResource;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService implements ProductServiceInterface
{
    protected $repository;

    public function __construct(ProductRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getProducts($data): LengthAwarePaginator
    {
        $page = $data['page'] ?? 1;
        $limit = $data['limit'] ?? 10;

        $filters = [
            'status' => ActiveStatus::Active->value,
        ];

        if (!empty($data['brand_id'])) {
            $filters['brand_id'] = $data['brand_id'];
        }

        if (!empty($data['keyword'])) {
            $filters[] = ['name', 'LIKE', "%{$data['keyword']}%"];
        }


        $query = $this->repository->getByQueryBuilder($filters);

        if (!empty($data['product_catalog_id'])) {
            $query->whereHas('productCatalogs', function ($query) use ($data) {
                $query->where('product_catalog_id', $data['product_catalog_id']);
            });
        }

        return $query->paginate($limit, ['*'], 'page', $page);
    }
}
