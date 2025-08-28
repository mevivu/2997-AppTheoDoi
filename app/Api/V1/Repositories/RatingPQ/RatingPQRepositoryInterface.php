<?php

namespace App\Api\V1\Repositories\RatingPQ;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface RatingPQRepositoryInterface extends EloquentRepositoryInterface
{
    public function exists(array $conditions): bool;

    public function getLatestByChildId($childId);

    public function getRecordInDateRange($childId, string $startDate, string $endDate, bool $oldest = false);


}
