<?php

namespace App\Api\V1\Repositories\Video;

use App\Admin\Repositories\Video\VideoRepository as AdminRepository;
use App\Models\Video;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class VideoRepository extends AdminRepository implements VideoRepositoryInterface
{
    public function getVideoList(array $filters = [], int $limit = 15): LengthAwarePaginator
    {
        $query = $this->model->active();

        if (!empty($filters['access_type'])) {
            $query->where('access_type', $filters['access_type']);
        }

        if (!empty($filters['keyword'])) {
            $query->where('title', 'like', '%' . $filters['keyword'] . '%');
        }

        return $query->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    public function findActiveWithCategory(int $id): ?Video
    {
        return $this->model->active()->find($id);
    }

    public function getRelatedVideos(int $videoId, ?int $categoryId, ?int $ageGroupId, int $limit = 8): Collection
    {
        return $this->model->active()
            ->where('id', '!=', $videoId)
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    public function incrementViewCount(Video|int $video): bool
    {
        if (is_numeric($video)) {
            $video = $this->model->find($video);
        }

        if ($video) {
            return (bool) $video->increment('view_count');
        }

        return false;
    }
}
