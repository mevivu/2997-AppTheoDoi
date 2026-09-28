<?php

namespace App\Api\V1\Services\Lesson;

use App\Api\V1\Repositories\AgeGroup\AgeGroupRepositoryInterface;
use App\Api\V1\Repositories\Lesson\LessonRepositoryInterface;
use App\Api\V1\Repositories\LessonCategory\LessonCategoryRepositoryInterface;
use App\Enums\Child\BornStatus;
use App\Enums\Lesson\LessonAccessType;
use App\Enums\Package\PackageType;
use App\Enums\Package\PackageUserStatus;
use App\Models\FeatureUsage;
use App\Models\Lesson;
use App\Models\LessonDifficultyRatingModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LessonService implements LessonServiceInterface
{
    protected LessonRepositoryInterface $lessonRepository;
    protected LessonCategoryRepositoryInterface $lessonCategoryRepository;
    protected AgeGroupRepositoryInterface $ageGroupRepository;

    public function __construct(
        LessonRepositoryInterface $lessonRepository,
        LessonCategoryRepositoryInterface $lessonCategoryRepository,
        AgeGroupRepositoryInterface $ageGroupRepository
    ) {
        $this->lessonRepository = $lessonRepository;
        $this->lessonCategoryRepository = $lessonCategoryRepository;
        $this->ageGroupRepository = $ageGroupRepository;
    }

    /**
     * Kiểm tra user hiện tại có gói VIP đang hoạt động hay không
     */
    public function isVipUser(): bool
    {
        $user = auth('api')->user();
        if (!$user) {
            return false;
        }

        return $user->userPackages()
            ->where('status', PackageUserStatus::Active)
            ->where('end_date', '>=', now())
            ->whereNotIn('current_type', [
                PackageType::Normal->value,
                PackageType::Trial->value,
                PackageType::Normal,
                PackageType::Trial,
            ])
            ->exists();
    }

    /**
     * Lấy danh sách nhóm độ tuổi (tự động phát hiện nhóm tuổi của bé nếu truyền child_id/child_age_months)
     */
    public function getAgeGroups(Request $request): Collection
    {
        $childAgeMonths = null;
        $isUnborn = false;

        if ($request->filled('child_id')) {
            $user = auth('api')->user();
            if (!$user) {
                throw new HttpException(401, 'Vui lòng đăng nhập để chọn hồ sơ của trẻ.');
            }

            $child = $user->children()->find($request->input('child_id'));
            if (!$child) {
                throw new HttpException(404, 'Không tìm thấy thông tin của trẻ hoặc bạn không có quyền truy cập hồ sơ này.');
            }

            if ($child->is_born == BornStatus::Unborn) {
                $isUnborn = true;
            } elseif ($child->birthday) {
                $childAgeMonths = Carbon::parse($child->birthday)->diffInMonths(now());
            }
        } elseif ($request->filled('child_age_months')) {
            $childAgeMonths = (int) $request->input('child_age_months');
        }

        if ($isUnborn) {
            $prenatalGroup = $this->ageGroupRepository->getPrenatalGroup();
            $otherGroups = $this->ageGroupRepository->getEarlyStageGroups(2);

            $groups = new Collection();
            if ($prenatalGroup) {
                $prenatalGroup->is_current = true;
                $groups->push($prenatalGroup);
            }
            foreach ($otherGroups as $g) {
                $g->is_current = false;
                $groups->push($g);
            }

            return $groups;
        }

        if ($childAgeMonths !== null) {
            $nearestIds = $this->ageGroupRepository->getNearestGroupIds($childAgeMonths, 3);
            $nearestGroups = $this->ageGroupRepository->getByIdsOrdered($nearestIds);

            $hasCurrent = false;
            foreach ($nearestGroups as $g) {
                $inRange = ($g->min_months === null || $g->min_months <= $childAgeMonths)
                    && ($g->max_months === null || $g->max_months >= $childAgeMonths);
                if ($inRange && !$hasCurrent && ($g->min_months !== null || $g->max_months !== null)) {
                    $g->is_current = true;
                    $hasCurrent = true;
                } else {
                    $g->is_current = false;
                }
            }

            if (!$hasCurrent && $nearestGroups->isNotEmpty() && !empty($nearestIds)) {
                $closestId = $nearestIds[0];
                foreach ($nearestGroups as $g) {
                    if ($g->id == $closestId) {
                        $g->is_current = true;
                        break;
                    }
                }
            }

            return $nearestGroups;
        }

        return $this->ageGroupRepository->getAllActiveOrdered();
    }

    /**
     * Lấy danh sách 5 trụ cột giáo dục kèm thống kê số bài học
     */
    public function getPillars(Request $request): array
    {
        $ageGroupId = $request->filled('age_group_id') ? (int) $request->input('age_group_id') : null;
        return $this->lessonCategoryRepository->getPillarsWithLessonCounts($ageGroupId);
    }

    /**
     * Lấy danh mục bài học / kỹ năng theo nhóm tuổi & trụ cột
     */
    public function getCategories(Request $request): Collection
    {
        $ageGroupId = $request->filled('age_group_id') ? (int) $request->input('age_group_id') : null;
        $pillar = $request->input('pillar');

        if (!empty($pillar)) {
            return $this->lessonCategoryRepository->getByPillar($pillar, $ageGroupId);
        }

        return $this->lessonCategoryRepository->getCategoriesByAgeGroup($ageGroupId);
    }

    /**
     * Lấy danh sách bài học phân trang kèm quyền truy cập Free/VIP
     */
    public function getLessonList(Request $request): array
    {
        $filters = [
            'lesson_category_id' => $request->input('lesson_category_id', $request->input('category_id')),
            'age_group_id' => $request->input('age_group_id'),
            'pillar' => $request->input('pillar'),
            'access_type' => $request->input('access_type'),
            'difficulty' => $request->input('difficulty'),
            'keyword' => $request->input('keyword'),
        ];

        $limit = max(1, min(50, (int) $request->input('limit', 15)));
        $lessons = $this->lessonRepository->getLessonList($filters, $limit);

        $isVip = $this->isVipUser();

        $lessons->getCollection()->transform(function ($lesson) use ($isVip) {
            $lesson->is_locked = $this->computeIsLocked($lesson, $isVip);
            return $lesson;
        });

        return [
            'lessons' => $lessons,
            'pagination' => [
                'current_page' => $lessons->currentPage(),
                'last_page' => $lessons->lastPage(),
                'per_page' => $lessons->perPage(),
                'total' => $lessons->total(),
            ],
        ];
    }

    /**
     * Lấy chi tiết bài học và danh sách bài học liên quan
     */
    public function getLessonDetail(int $id): array
    {
        $lesson = $this->lessonRepository->findActiveWithRelations($id);
        if (!$lesson) {
            throw new HttpException(404, 'Không tìm thấy bài học hoặc bài học đã bị ẩn');
        }

        $isVip = $this->isVipUser();
        $lesson->is_locked = $this->computeIsLocked($lesson, $isVip);

        $relatedLessons = $this->lessonRepository->getRelatedLessons(
            $lesson->id,
            $lesson->lesson_category_id,
            $lesson->age_group_id,
            $lesson->category?->pillar?->value,
            6
        );

        $relatedLessons->transform(function ($item) use ($isVip) {
            $item->is_locked = $this->computeIsLocked($item, $isVip);
            return $item;
        });

        // Embed thống kê đánh giá độ khó vào lesson detail
        $childId = request()->filled('child_id') ? (int) request()->input('child_id') : null;
        $lesson->difficulty_rating_data = $this->getDifficultyStats($lesson->id, $childId);

        return [
            'lesson' => $lesson,
            'related_lessons' => $relatedLessons,
        ];
    }

    /**
     * Tăng lượt xem bài học kèm bảo vệ chống spam (2 giờ / user)
     */
    public function incrementView(Request $request, int $id): array
    {
        $lesson = $this->lessonRepository->findActiveWithRelations($id);
        if (!$lesson) {
            throw new HttpException(404, 'Không tìm thấy bài học hoặc bài học đã bị ẩn');
        }

        $isVip = $this->isVipUser();
        if ($lesson->access_type == LessonAccessType::Vip && !$isVip) {
            throw new HttpException(403, 'Bạn cần đăng ký gói VIP để học bài này.');
        }

        $user = auth('api')->user();
        $identifier = $user ? "user_{$user->id}" : ('ip_' . md5($request->ip() . '_' . ($request->userAgent() ?? '')));
        $cacheKey = "lesson_view:{$lesson->id}:{$identifier}";

        if (!Cache::has($cacheKey)) {
            $this->lessonRepository->incrementViewCount($lesson);
            $lesson->refresh();
            Cache::put($cacheKey, 1, now()->addHours(2));

            // Ghi nhận sự kiện thống kê tính năng
            if (Schema::hasTable('feature_usages')) {
                FeatureUsage::create([
                    'user_id' => $user?->id,
                    'child_id' => $request->input('child_id'),
                    'feature_code' => 'lesson_education',
                    'feature_name' => 'Bài học giáo dục 5 trụ cột',
                    'category' => 'education',
                    'action' => 'watch_lesson',
                    'metadata' => json_encode([
                        'lesson_id' => $lesson->id,
                        'name' => $lesson->name,
                        'pillar' => $lesson->category?->pillar?->value,
                    ]),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        }

        return [
            'id' => $lesson->id,
            'view_count' => (int) $lesson->view_count,
        ];
    }

    /**
     * Tính toán trạng thái khóa bài học cho user hiện tại
     */
    protected function computeIsLocked($lesson, bool $isVip): bool
    {
        if ($isVip) {
            return false;
        }

        if (($lesson->access_type ?? LessonAccessType::Free) === LessonAccessType::Free) {
            return false;
        }

        return true;
    }

    /**
     * Toggle đánh giá độ khó bài học (tạo / cập nhật / xóa)
     *
     * Logic:
     * - Chưa có rating → tạo mới
     * - Đã có, cùng mức → xóa (toggle off)
     * - Đã có, khác mức → cập nhật sang mức mới
     */
    public function toggleDifficultyRating(int $lessonId, int $childId, string $level): array
    {
        $user = auth('api')->user();
        if (!$user) {
            throw new HttpException(401, 'Vui lòng đăng nhập để đánh giá bài học.');
        }

        // Validate child thuộc về user hiện tại
        $child = $user->children()->find($childId);
        if (!$child) {
            throw new HttpException(403, 'Bạn không có quyền đánh giá cho hồ sơ bé này.');
        }

        // Validate lesson tồn tại và đang active
        $lesson = $this->lessonRepository->findActiveWithRelations($lessonId);
        if (!$lesson) {
            throw new HttpException(404, 'Không tìm thấy bài học hoặc bài học đã bị ẩn.');
        }

        // Tìm rating hiện tại
        $existing = LessonDifficultyRatingModel::where([
            'user_id' => $user->id,
            'child_id' => $child->id,
            'lesson_id' => $lesson->id,
        ])->first();

        $action = 'created';
        $currentRating = $level;

        if ($existing) {
            if ($existing->difficulty_level->value === $level) {
                // Toggle OFF: bỏ chọn
                $existing->delete();
                $action = 'removed';
                $currentRating = null;
            } else {
                // Đổi mức đánh giá
                $existing->update(['difficulty_level' => $level]);
                $action = 'updated';
            }
        } else {
            // Tạo mới
            LessonDifficultyRatingModel::create([
                'user_id' => $user->id,
                'child_id' => $child->id,
                'lesson_id' => $lesson->id,
                'difficulty_level' => $level,
            ]);
        }

        return [
            'action' => $action,
            'current_rating' => $currentRating,
            'stats' => $this->getDifficultyStats($lessonId, $childId),
        ];
    }

    /**
     * Lấy thống kê đánh giá độ khó bài học (phần trăm mỗi mức)
     */
    public function getDifficultyStats(int $lessonId, ?int $childId = null): array
    {
        $counts = LessonDifficultyRatingModel::where('lesson_id', $lessonId)
            ->selectRaw('difficulty_level, COUNT(*) as count')
            ->groupBy('difficulty_level')
            ->pluck('count', 'difficulty_level')
            ->toArray();

        $total = array_sum($counts);

        $stats = [];
        foreach (['easy', 'with_help', 'hard'] as $level) {
            $count = $counts[$level] ?? 0;
            $stats[$level] = [
                'count' => $count,
                'percent' => $total > 0 ? round(($count / $total) * 100) : 0,
            ];
        }

        // Lấy rating hiện tại của user (nếu đang đăng nhập)
        $currentRating = null;
        $user = auth('api')->user();
        if ($user && $childId) {
            $currentRating = LessonDifficultyRatingModel::where([
                'user_id' => $user->id,
                'child_id' => $childId,
                'lesson_id' => $lessonId,
            ])->value('difficulty_level');
        }

        return [
            'current_rating' => $currentRating,
            'stats' => [
                'total_ratings' => $total,
                ...$stats,
            ],
        ];
    }
}
