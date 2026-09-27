<?php

namespace App\Api\V1\Repositories\Video;

use App\Admin\Repositories\Video\VideoRepositoryInterface as AdminRepositoryInterface;
use App\Models\Video;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface VideoRepositoryInterface extends AdminRepositoryInterface
{
    /**
     * Lấy danh sách video phân trang kèm bộ lọc
     */
    public function getVideoList(array $filters = [], int $limit = 15): LengthAwarePaginator;

    /**
     * Tìm video đang kích hoạt kèm thông tin danh mục
     */
    public function findActiveWithCategory(int $id): ?Video;

    /**
     * Lấy danh sách video liên quan (cùng danh mục hoặc cùng nhóm tuổi)
     */
    public function getRelatedVideos(int $videoId, ?int $categoryId, ?int $ageGroupId, int $limit = 8): Collection;

    /**
     * Tăng số lượt xem của video
     */
    public function incrementViewCount(Video|int $video): bool;
}
