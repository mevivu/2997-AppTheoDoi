<?php

namespace App\Api\V1\Repositories\LessonCategory;

use App\Admin\Repositories\LessonCategory\LessonCategoryRepositoryInterface as AdminRepositoryInterface;
use App\Enums\Lesson\EducationPillar;
use Illuminate\Database\Eloquent\Collection;

interface LessonCategoryRepositoryInterface extends AdminRepositoryInterface
{
    /**
     * Lấy danh mục theo nhóm tuổi và trụ cột giáo dục kèm số lượng bài học
     */
    public function getByAgeGroupAndPillar(int $ageGroupId, string|EducationPillar $pillar): Collection;

    /**
     * Lấy thống kê số bài học của 5 trụ cột giáo dục (tùy chọn theo nhóm tuổi)
     */
    public function getPillarsWithLessonCounts(?int $ageGroupId = null): array;

    /**
     * Lấy tất cả danh mục hoạt động theo nhóm tuổi
     */
    public function getCategoriesByAgeGroup(int $ageGroupId): Collection;
}
