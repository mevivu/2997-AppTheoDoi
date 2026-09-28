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
     * Lấy danh mục theo nhóm tuổi và trụ cột giáo dục kèm số lượng bài học
     */
    public function getByAgeGroupAndPillar(int $ageGroupId, string|EducationPillar $pillar): Collection
    {
        $pillarValue = $pillar instanceof EducationPillar ? $pillar->value : $pillar;

        return $this->model
            ->active()
            ->where('age_group_id', $ageGroupId)
            ->where('pillar', $pillarValue)
            ->withCount([
                'lessons as lessons_count' => function ($q) {
                    $q->where('status', ActiveStatus::Active->value);
                },
                'lessons as free_lessons_count' => function ($q) {
                    $q->where('status', ActiveStatus::Active->value)
                      ->where('access_type', LessonAccessType::Free->value);
                },
                'lessons as vip_lessons_count' => function ($q) {
                    $q->where('status', ActiveStatus::Active->value)
                      ->where('access_type', LessonAccessType::Vip->value);
                },
            ])
            ->with('ageGroup')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * Lấy thống kê số bài học của 5 trụ cột giáo dục (tùy chọn theo nhóm tuổi)
     */
    public function getPillarsWithLessonCounts(?int $ageGroupId = null): array
    {
        $query = DB::table('lessons')
            ->join('lesson_categories', 'lessons.lesson_category_id', '=', 'lesson_categories.id')
            ->where('lessons.status', ActiveStatus::Active->value)
            ->where('lesson_categories.status', ActiveStatus::Active->value);

        if ($ageGroupId !== null) {
            $query->where('lesson_categories.age_group_id', $ageGroupId);
        }

        $results = $query
            ->select('lesson_categories.pillar', DB::raw('count(lessons.id) as total_lessons'))
            ->groupBy('lesson_categories.pillar')
            ->pluck('total_lessons', 'pillar')
            ->toArray();

        $pillars = [];
        $order = 1;
        foreach (EducationPillar::cases() as $pillarCase) {
            $pillars[$pillarCase->value] = [
                'key' => $pillarCase->value,
                'name' => $pillarCase->label(),
                'short_name' => match ($pillarCase) {
                    EducationPillar::PQ => 'Thể chất',
                    EducationPillar::IQ => 'Trí tuệ',
                    EducationPillar::EQ => 'Cảm xúc',
                    EducationPillar::AQ => 'Vượt khó',
                    EducationPillar::THAI_GIAO => 'Học tập',
                },
                'icon' => $pillarCase->icon(),
                'color' => $pillarCase->color(),
                'active_color' => '#196C74',
                'order' => $order++,
                'lessons_count' => (int) ($results[$pillarCase->value] ?? 0),
            ];
        }

        return array_values($pillars);
    }

    /**
     * Lấy tất cả danh mục hoạt động theo nhóm tuổi
     */
    public function getCategoriesByAgeGroup(int $ageGroupId): Collection
    {
        return $this->model
            ->active()
            ->where('age_group_id', $ageGroupId)
            ->withCount([
                'lessons as lessons_count' => function ($q) {
                    $q->where('status', ActiveStatus::Active->value);
                },
                'lessons as free_lessons_count' => function ($q) {
                    $q->where('status', ActiveStatus::Active->value)
                      ->where('access_type', LessonAccessType::Free->value);
                },
                'lessons as vip_lessons_count' => function ($q) {
                    $q->where('status', ActiveStatus::Active->value)
                      ->where('access_type', LessonAccessType::Vip->value);
                },
            ])
            ->with('ageGroup')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }
}
