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
            ->where(function ($query) {
                $query->where('height', '>', 0)
                    ->orWhere('weight', '>', 0);
            })
            ->orderBy('assessment_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();
    }

    public function getRecordInDateRange($childId, string $startDate, string $endDate, bool $oldest = false)
    {
        $query = $this->getQueryBuilder()
            ->where('child_id', $childId)
            ->whereBetween('assessment_date', [$startDate, $endDate]);

        if ($oldest) {
            $query->oldest('assessment_date');
        } else {
            $query->orderBy('assessment_date', 'desc')
                ->orderBy('id', 'desc');
        }

        return $query->first();
    }

    public function getRecordInDateRangeWithValidStrengthEndurance($childId, string $startDate, string $endDate, bool $oldest = false)
    {
        $query = $this->model
            ->where('child_id', $childId)
            ->whereBetween('assessment_date', [$startDate, $endDate])
            ->whereNotNull('strength')
            ->whereNotNull('endurance')
            ->where('strength', '!=', 0)
            ->where('endurance', '!=', 0);

        if ($oldest) {
            $query->orderBy('assessment_date', 'asc');
        } else {
            $query->orderBy('assessment_date', 'desc');
        }

        return $query->first();
    }
}
