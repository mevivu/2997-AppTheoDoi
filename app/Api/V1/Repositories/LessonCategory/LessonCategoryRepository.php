<?php

namespace App\Api\V1\Repositories\LessonCategory;

use App\Admin\Repositories\LessonCategory\LessonCategoryRepository as AdminRepository;
use App\Enums\ActiveStatus;
use App\Enums\Lesson\EducationPillar;
use App\Enums\Lesson\LessonAccessType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class LessonCategoryRepository extends AdminRepository implements LessonCategoryRepositoryInterface
{
    /**
     * Lấy danh mục theo trụ cột giáo dục kèm số lượng bài học (tùy chọn theo nhóm tuổi)
     */
    public function getByPillar(string|EducationPillar $pillar, ?int $ageGroupId = null): Collection
    {
        $pillarValue = $pillar instanceof EducationPillar ? $pillar->value : $pillar;

        return $this->model
            ->active()
            ->where('pillar', $pillarValue)
            ->withCount([
                'lessons as lessons_count' => function ($q) use ($ageGroupId) {
                    $q->where('status', ActiveStatus::Active->value);
                    if ($ageGroupId !== null) {
                        $q->where('age_group_id', $ageGroupId);
                    }
                },
                'lessons as free_lessons_count' => function ($q) use ($ageGroupId) {
                    $q->where('status', ActiveStatus::Active->value)
                      ->where('access_type', LessonAccessType::Free->value);
                    if ($ageGroupId !== null) {
                        $q->where('age_group_id', $ageGroupId);
                    }
                },
                'lessons as vip_lessons_count' => function ($q) use ($ageGroupId) {
                    $q->where('status', ActiveStatus::Active->value)
                      ->where('access_type', LessonAccessType::Vip->value);
                    if ($ageGroupId !== null) {
                        $q->where('age_group_id', $ageGroupId);
                    }
                },
            ])
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * Lấy danh mục theo nhóm tuổi và trụ cột giáo dục kèm số lượng bài học (tương thích ngược)
     */
    public function getByAgeGroupAndPillar(?int $ageGroupId, string|EducationPillar $pillar): Collection
    {
        return $this->getByPillar($pillar, $ageGroupId);
    }

    /**
     * Lấy thống kê số bài học của 4 trụ cột giáo dục (tùy chọn theo nhóm tuổi)
     */
    public function getPillarsWithLessonCounts(?int $ageGroupId = null): array
    {
        $query = DB::table('lessons')
            ->join('lesson_categories', 'lessons.lesson_category_id', '=', 'lesson_categories.id')
            ->where('lessons.status', ActiveStatus::Active->value)
            ->where('lesson_categories.status', ActiveStatus::Active->value);

        if ($ageGroupId !== null) {
            $query->where('lessons.age_group_id', $ageGroupId);
        }

        $results = $query
            ->select('lesson_categories.pillar', DB::raw('count(lessons.id) as total_lessons'))
            ->groupBy('lesson_categories.pillar')
            ->pluck('total_lessons', 'pillar')
            ->toArray();

        $pillars = [];
        $order = 1;
        $orderedCases = [
            EducationPillar::IQ,
            EducationPillar::EQ,
            EducationPillar::PQ,
            EducationPillar::AQ,
            EducationPillar::HOC_TAP,
        ];
        foreach ($orderedCases as $pillarCase) {
            $pillars[$pillarCase->value] = [
                'key' => $pillarCase->value,
                'name' => $pillarCase->label(),
                'short_name' => $pillarCase->shortName(),
                'description' => $pillarCase->description(),
                'icon' => $pillarCase->icon(),
                'image_url' => $pillarCase->imageUrl(),
                'color' => $pillarCase->color(),
                'soft_color' => $pillarCase->softColor(),
                'active_color' => '#196C74',
                'order' => $order++,
                'lessons_count' => (int) ($results[$pillarCase->value] ?? 0),
            ];
        }

        return array_values($pillars);
    }

    /**
     * Lấy tất cả danh mục hoạt động (tùy chọn theo nhóm tuổi)
     */
    public function getCategoriesByAgeGroup(?int $ageGroupId = null): Collection
    {
        return $this->model
            ->active()
            ->withCount([
                'lessons as lessons_count' => function ($q) use ($ageGroupId) {
                    $q->where('status', ActiveStatus::Active->value);
                    if ($ageGroupId !== null) {
                        $q->where('age_group_id', $ageGroupId);
                    }
                },
                'lessons as free_lessons_count' => function ($q) use ($ageGroupId) {
                    $q->where('status', ActiveStatus::Active->value)
                      ->where('access_type', LessonAccessType::Free->value);
                    if ($ageGroupId !== null) {
                        $q->where('age_group_id', $ageGroupId);
                    }
                },
                'lessons as vip_lessons_count' => function ($q) use ($ageGroupId) {
                    $q->where('status', ActiveStatus::Active->value)
                      ->where('access_type', LessonAccessType::Vip->value);
                    if ($ageGroupId !== null) {
                        $q->where('age_group_id', $ageGroupId);
                    }
                },
            ])
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }
}
