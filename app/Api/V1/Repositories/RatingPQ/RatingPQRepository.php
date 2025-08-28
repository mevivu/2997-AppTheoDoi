<?php

namespace App\Api\V1\Repositories\RatingPQ;

use App\Admin\Repositories\RatingPQ\RatingPQRepository as AdminRepository;
use App\Models\RatingPQ;


class RatingPQRepository extends AdminRepository implements RatingPQRepositoryInterface
{
    public function exists(array $conditions): bool
    {
        return RatingPQ::where($conditions)->exists();
    }

    public function getLatestByChildId($childId)
    {
        return $this->getQueryBuilder()
            ->where('child_id', $childId)
            ->orderBy('assessment_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();
    }
}
