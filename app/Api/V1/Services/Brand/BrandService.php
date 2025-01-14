<?php

namespace App\Api\V1\Services\Brand;

use App\Api\V1\Repositories\Brand\BrandRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class BrandService implements BrandServiceInterface
{
    protected $repository;

    public function __construct(BrandRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getBrands($data): LengthAwarePaginator
    {
        $page = $data['page'] ?? 1;
        $limit = $data['limit'] ?? 10;

        $filters = [];

        if (!empty($data['keyword'])) {
            $filters[] = ['name', 'LIKE', "%{$data['keyword']}%"];
        }

        if (!empty($data['status'])) {
            $filters['status'] = $data['status'];
        }

        $query = $this->repository->getByQueryBuilder($filters);

        return $query->paginate($limit, ['*'], 'page', $page);
    }
}
