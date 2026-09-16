<?php

namespace App\Admin\Repositories\MemoRating;

use App\Admin\Repositories\EloquentRepository;
use App\Models\MemoRating;

class MemoRatingRepository extends EloquentRepository implements MemoRatingRepositoryInterface
{
    public function getModel(): string
    {
        return MemoRating::class;
    }

    public function getRatingsByChild(int $childId)
    {
        return $this->model->where('child_id', $childId)
            ->with(['theme', 'ageConfig', 'rounds'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
