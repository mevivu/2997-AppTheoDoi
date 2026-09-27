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
        $query = $this->model->active()->with('category');

        $categoryId = $filters['video_category_id'] ?? ($filters['category_id'] ?? null);
        if ($categoryId) {
            $query->where('video_category_id', $categoryId);
        }

        if (!empty($filters['age_group_id'])) {
            $ageGroupId = $filters['age_group_id'];
            $query->whereHas('category', function ($q) use ($ageGroupId) {
                $q->where('age_group_id', $ageGroupId);
            });
        }

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
        return $this->model->active()->with('category')->find($id);
    }

    public function getRelatedVideos(int $videoId, ?int $categoryId, ?int $ageGroupId, int $limit = 8): Collection
    {
        $relatedQuery = $this->model->active()
            ->where('id', '!=', $videoId);

        if ($categoryId) {
            $relatedQuery->where('video_category_id', $categoryId);
        }

        $relatedVideos = $relatedQuery->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();

        // Nếu cùng danh mục có ít hơn 4 video, lấy bổ sung các video cùng nhóm tuổi
        if ($relatedVideos->count() < 4 && $ageGroupId) {
            $excludedIds = $relatedVideos->pluck('id')->push($videoId)->all();

            $moreVideos = $this->model->active()
                ->whereNotIn('id', $excludedIds)
                ->whereHas('category', function ($q) use ($ageGroupId) {
                    $q->where('age_group_id', $ageGroupId);
                })
                ->orderBy('sort_order', 'asc')
                ->orderBy('created_at', 'desc')
                ->take($limit - $relatedVideos->count())
                ->get();

            $relatedVideos = $relatedVideos->concat($moreVideos);
        }

        return $relatedVideos;
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
