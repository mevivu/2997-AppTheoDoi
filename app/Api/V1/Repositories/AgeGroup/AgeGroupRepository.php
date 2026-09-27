<?php

namespace App\Api\V1\Repositories\AgeGroup;

use App\Admin\Repositories\AgeGroup\AgeGroupRepository as AdminRepository;
use App\Models\AgeGroup;
use Illuminate\Database\Eloquent\Collection;

class AgeGroupRepository extends AdminRepository implements AgeGroupRepositoryInterface
{
    public function getPrenatalGroup(): ?AgeGroup
    {
        return $this->model
            ->active()
            ->whereNull('min_months')
            ->whereNull('max_months')
            ->first();
    }

    public function getEarlyStageGroups(int $limit = 2): Collection
    {
        return $this->model
            ->active()
            ->whereNotNull('min_months')
            ->orderBy('min_months', 'asc')
            ->take($limit)
            ->get();
    }

    public function getNearestGroupIds(int $childAgeMonths, int $limit = 3): array
    {
        return $this->model
            ->nearestToChild($childAgeMonths)
            ->take($limit)
            ->pluck('id')
            ->toArray();
    }

    public function getByIdsOrdered(array $ids): Collection
    {
        return $this->model
            ->active()
            ->whereIn('id', $ids)
            ->orderBy('sort_order', 'asc')
            ->orderByRaw('CASE WHEN min_months IS NULL THEN 0 ELSE 1 END')
            ->orderBy('min_months', 'asc')
            ->get();
    }

    public function getAllActiveOrdered(): Collection
    {
        return $this->model
            ->active()
            ->orderBy('sort_order', 'asc')
            ->orderByRaw('CASE WHEN min_months IS NULL THEN 0 ELSE 1 END')
            ->orderBy('min_months', 'asc')
            ->get();
    }
}
