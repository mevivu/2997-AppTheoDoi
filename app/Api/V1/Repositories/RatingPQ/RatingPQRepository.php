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
}
