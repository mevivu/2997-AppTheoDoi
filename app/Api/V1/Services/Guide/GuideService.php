<?php

namespace App\Api\V1\Services\Guide;

use App\Enums\ActiveStatus;
use App\Api\V1\Repositories\Guide\GuideRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class GuideService implements GuideServiceInterface
{
    protected $repository;

    public function __construct(GuideRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getGuides(array $data): LengthAwarePaginator
    {
        $page = $data['page'] ?? 1;
        $limit = $data['limit'] ?? 10;
        $type = $data['type'] ?? null;

        // Build query with filters
        $filters = [
            'status' => ActiveStatus::Active->value,
        ];

        if ($type) {
            $filters['type'] = $type;
        }

        // Return the paginated result
        return $this->repository
            ->getByQueryBuilder($filters)
            ->paginate($limit, ['*'], 'page', $page);
    }
}
