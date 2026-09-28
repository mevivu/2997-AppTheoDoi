<?php

namespace App\Api\V1\Repositories\Lesson;

use App\Admin\Repositories\Lesson\LessonRepositoryInterface as AdminRepositoryInterface;
use App\Models\Lesson;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface LessonRepositoryInterface extends AdminRepositoryInterface
{
    /**
     * Lấy danh sách bài học phân trang theo bộ lọc đa chiều
     */
    public function getLessonList(array $filters, int $limit = 15): LengthAwarePaginator;

    /**
     * Lấy bài học hoạt động kèm quan hệ (category, ageGroup, videos)
     */
    public function findActiveWithRelations(int $id): ?Lesson;

    /**
     * Lấy danh sách bài học liên quan
     */
    public function getRelatedLessons(
        int $currentLessonId,
        ?int $categoryId,
        ?int $ageGroupId,
        ?string $pillar,
        int $limit = 6
    ): Collection;

    /**
     * Tăng số lượt xem của bài học
     */
    public function incrementViewCount(Lesson $lesson): void;
}
