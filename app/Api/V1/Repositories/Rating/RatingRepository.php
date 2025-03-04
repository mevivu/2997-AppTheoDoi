<?php

namespace App\Api\V1\Repositories\Rating;

use App\Admin\Repositories\Rating\RatingRepository as AdminRepository;
use App\Models\Rating;


class RatingRepository extends AdminRepository implements RatingRepositoryInterface
{
    public function exists(array $conditions): bool
    {
        return Rating::where($conditions)->exists();
    }
}
