<?php

namespace App\Admin\Repositories\MemoRating;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface MemoRatingRepositoryInterface extends EloquentRepositoryInterface
{
    public function getRatingsByChild(int $childId);
}
