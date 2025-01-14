<?php

namespace App\Api\V1\Services\ProductCatalog;

use App\Api\V1\Repositories\ProductCatalog\ProductCatalogRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductCatalogService implements ProductCatalogServiceInterface
{
    protected $repository;

    public function __construct(ProductCatalogRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getProductCatalogs($data): LengthAwarePaginator
    {
        $page = $data['page'] ?? 1;
        $limit = $data['limit'] ?? 10;

        $filters = [];


        if (!empty($data['status'])) {
            $filters['status'] = $data['status'];
        }

        $query = $this->repository->getByQueryBuilder($filters);

        return $query->paginate($limit, ['*'], 'page', $page);
    }
}
