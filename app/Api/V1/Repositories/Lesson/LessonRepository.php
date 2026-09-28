<?php

namespace App\Api\V1\Repositories\Lesson;

use App\Admin\Repositories\Lesson\LessonRepository as AdminRepository;
use App\Enums\ActiveStatus;
use App\Enums\Lesson\EducationPillar;
use App\Enums\Lesson\LessonAccessType;
use App\Enums\Lesson\LessonDifficulty;
use App\Models\Lesson;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class LessonRepository extends AdminRepository implements LessonRepositoryInterface
{
    /**
     * Lấy danh sách bài học phân trang theo bộ lọc đa chiều
     */
    public function getLessonList(array $filters, int $limit = 15): LengthAwarePaginator
    {
        $query = $this->model
            ->active()
            ->with([
                'category' => function ($q) {
                    $q->select('id', 'name', 'slug', 'pillar', 'age_group_id', 'icon')
                      ->with('ageGroup:id,name,min_months,max_months');
                },
                'videos' => function ($q) {
                    $q->orderBy('sort_order', 'asc');
                },
            ]);

        // Lọc theo danh mục bài học
        $categoryId = $filters['lesson_category_id'] ?? $filters['category_id'] ?? null;
        if (!empty($categoryId)) {
            $query->where('lesson_category_id', $categoryId);
        }

        // Lọc theo nhóm độ tuổi
        if (!empty($filters['age_group_id'])) {
            $query->whereHas('category', function ($q) use ($filters) {
                $q->where('age_group_id', $filters['age_group_id']);
            });
        }

        // Lọc theo trụ cột giáo dục
        if (!empty($filters['pillar'])) {
            $pillar = $filters['pillar'] instanceof EducationPillar ? $filters['pillar']->value : $filters['pillar'];
            $query->whereHas('category', function ($q) use ($pillar) {
                $q->where('pillar', $pillar);
            });
        }

        // Lọc theo phân quyền truy cập (free, vip)
        if (!empty($filters['access_type']) && $filters['access_type'] !== 'all') {
            $accessType = $filters['access_type'] instanceof LessonAccessType ? $filters['access_type']->value : $filters['access_type'];
            $query->where('access_type', $accessType);
        }

        // Lọc theo độ khó (easy, medium, hard)
        if (!empty($filters['difficulty'])) {
            $difficulty = $filters['difficulty'] instanceof LessonDifficulty ? $filters['difficulty']->value : $filters['difficulty'];
            $query->where('difficulty', $difficulty);
        }

        // Tìm kiếm theo từ khóa
        if (!empty($filters['keyword'])) {
            $keyword = trim($filters['keyword']);
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%")
                  ->orWhere('benefit', 'like', "%{$keyword}%");
            });
        }

        return $query
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->paginate($limit);
    }

    /**
     * Lấy bài học hoạt động kèm quan hệ
     */
    public function findActiveWithRelations(int $id): ?Lesson
    {
        return $this->model
            ->active()
            ->with([
                'category' => function ($q) {
                    $q->with('ageGroup');
                },
                'videos' => function ($q) {
                    $q->orderBy('sort_order', 'asc');
                },
            ])
            ->find($id);
    }

    /**
     * Lấy danh sách bài học liên quan
     */
    public function getRelatedLessons(
        int $currentLessonId,
        ?int $categoryId,
        ?int $ageGroupId,
        ?string $pillar,
        int $limit = 6
    ): Collection {
        $query = $this->model
            ->active()
            ->where('id', '!=', $currentLessonId)
            ->with([
                'category:id,name,slug,pillar,age_group_id',
                'videos' => function ($q) {
                    $q->orderBy('sort_order', 'asc');
                },
            ]);

        if ($categoryId) {
            $query->where('lesson_category_id', $categoryId);
        } elseif ($pillar && $ageGroupId) {
            $query->whereHas('category', function ($q) use ($pillar, $ageGroupId) {
                $q->where('pillar', $pillar)
                  ->where('age_group_id', $ageGroupId);
            });
        } elseif ($pillar) {
            $query->whereHas('category', function ($q) use ($pillar) {
                $q->where('pillar', $pillar);
            });
        }

        return $query
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Tăng số lượt xem của bài học
     */
    public function incrementViewCount(Lesson $lesson): void
    {
        $lesson->increment('view_count');
    }
}
