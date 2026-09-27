<?php

namespace App\Api\V1\Repositories\VideoCategory;

use App\Admin\Repositories\VideoCategory\VideoCategoryRepository as AdminRepository;
use Illuminate\Database\Eloquent\Collection;

class VideoCategoryRepository extends AdminRepository implements VideoCategoryRepositoryInterface
{
    public function getCategoriesWithCounts(?int $ageGroupId = null): Collection
    {
        $query = $this->model->active()
            ->with('ageGroup')
            ->withCount([
                'videos as videos_count' => fn ($q) => $q->active(),
                'videos as free_videos_count' => fn ($q) => $q->active()->free(),
                'videos as vip_videos_count' => fn ($q) => $q->active()->vip(),
            ]);

        if ($ageGroupId) {
            $query->where('age_group_id', $ageGroupId);
        }

        return $query->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
