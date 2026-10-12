<?php

namespace App\Services\Assessment;

use App\Enums\ActiveStatus;
use App\Enums\Child\BornStatus;
use App\Enums\Lesson\LessonAccessType;
use App\Enums\Package\PackageType;
use App\Enums\Package\PackageUserStatus;
use App\Enums\Question\QuestionType;
use App\Models\AgeGroup;
use App\Models\Child;
use App\Models\Lesson;
use App\Models\Rating;
use Carbon\Carbon;

class AssessmentRecommendationService
{
    /**
     * Xây dựng khối phân tích nhận định (Band, Parent Action) và gợi ý Video cải thiện
     *
     * @param Rating $rating Bản ghi đánh giá
     * @param Child|null $child Thông tin bé (nếu null sẽ lấy từ $rating->child)
     * @return array
     */
    public function buildAnalysis(Rating $rating, ?Child $child = null): array
    {
        $type = $rating->type instanceof QuestionType ? $rating->type->value : (string) $rating->type;
        $score = floatval($rating->score ?? 0);
        $child = $child ?: $rating->child;

        // 1. Phân bậc điểm, nhãn, đặc điểm và hành động cho bố mẹ
        $bandInfo = AssessmentBandResolver::resolve($type, $score);
        $parentAction = $rating->parent_action ?: $bandInfo['parent_action'];

        // 2. Lấy snapshot category_scores (hoặc tái tạo từ các cột nếu bản ghi cũ)
        $categories = $this->resolveCategoryScores($rating);

        // 3. Xây dựng đề xuất video cải thiện cho các danh mục có điểm <= weak_threshold
        $recommendation = $this->buildRecommendation($type, $categories, $child);

        return [
            'type' => $type,
            'score' => $score,
            'band' => [
                'key' => $bandInfo['band'],
                'label' => $bandInfo['label'] ?: ($rating->label ?? ''),
                'description' => $bandInfo['description'] ?: ($rating->description ?? ''),
            ],
            'parent_action' => $parentAction,
            'categories' => array_values($categories),
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Lấy snapshot điểm danh mục hoặc khôi phục từ các cột lưu trữ
     */
    public function resolveCategoryScores(Rating $rating): array
    {
        if (!empty($rating->category_scores) && is_array($rating->category_scores)) {
            return $rating->category_scores;
        }

        $type = $rating->type instanceof QuestionType ? $rating->type->value : (string) $rating->type;
        $categoriesConfig = config("assessment.categories.{$type}", []);
        $weakThreshold = (float) config('assessment.weak_threshold', 6.0);
        $categories = [];

        foreach ($categoriesConfig as $catKey => $conf) {
            $col = $conf['rating_column'];
            $val = $rating->{$col} ?? null;
            $score = null;
            $raw = null;

            if ($val !== null && $val !== '') {
                if ($type === QuestionType::IQ->value && str_contains((string) $val, '/')) {
                    $parts = explode('/', (string) $val);
                    $cor = (float) ($parts[0] ?? 0);
                    $tot = (float) ($parts[1] ?? 1);
                    $score = $tot > 0 ? round(($cor / $tot) * 10, 1) : null;
                    $raw = (string) $val;
                } else {
                    $score = floatval($val);
                    $raw = "{$score}/10";
                }
            }

            $isWeak = $score !== null && $score <= $weakThreshold;

            $categories[$catKey] = [
                'key' => $catKey,
                'rating_column' => $col,
                'label' => $conf['label'],
                'pillar' => $conf['pillar'],
                'lesson_category_key' => $conf['lesson_category_key'],
                'score' => $score,
                'raw' => $raw,
                'is_weak' => $isWeak,
            ];
        }

        return $categories;
    }

    /**
     * Dựng gợi ý video bài học cho các nhóm năng lực điểm yếu
     */
    protected function buildRecommendation(string $type, array $categories, ?Child $child): array
    {
        $weakCategories = array_filter($categories, fn($cat) => !empty($cat['is_weak']) && $cat['score'] !== null);

        if (empty($weakCategories)) {
            return [
                'has_weak' => false,
                'weak_categories' => [],
                'message' => null,
                'lesson_filter' => null,
                'lessons' => [],
            ];
        }

        // Sắp xếp các danh mục yếu theo điểm tăng dần (ưu tiên cải thiện điểm thấp nhất trước)
        usort($weakCategories, fn($a, $b) => ($a['score'] <=> $b['score']));

        $maxWeak = (int) config('assessment.max_weak_categories', 3);
        $selectedWeak = array_slice($weakCategories, 0, $maxWeak);

        $weakLabels = array_column($selectedWeak, 'label');
        $weakKeys = array_column($selectedWeak, 'lesson_category_key');
        $childName = $child?->fullname ? trim($child->fullname) : 'con';
        $weakLabelsText = implode(', ', $weakLabels);

        // Câu thông báo đề xuất chuẩn theo file PDF:
        // "Bố mẹ cần xem Video hướng dẫn cải thiện <Danh mục>,<Danh mục 2>,... cho con <Trẻ em> trong Mục Video Giáo dục."
        $message = "Bố mẹ cần xem Video hướng dẫn cải thiện {$weakLabelsText} cho con {$childName} trong Mục Video Giáo dục.";

        // Xác định nhóm tuổi của trẻ
        $ageGroupId = $this->resolveChildAgeGroupId($child);

        // Truy xuất bài học thực tế
        $isVip = $this->isVipUser();
        $lessons = $this->fetchLessonsForCategories($type, $weakKeys, $ageGroupId, $isVip);

        return [
            'has_weak' => true,
            'weak_categories' => array_values($selectedWeak),
            'message' => $message,
            'lesson_filter' => [
                'pillar' => $type,
                'category_keys' => $weakKeys,
                'age_group_id' => $ageGroupId,
                'child_id' => $child?->id,
            ],
            'lessons' => $lessons,
        ];
    }

    /**
     * Lấy danh sách bài học video theo các danh mục yếu và nhóm tuổi
     */
    protected function fetchLessonsForCategories(
        string $pillar,
        array $categoryKeys,
        ?int $ageGroupId,
        bool $isVip
    ): array {
        if (empty($categoryKeys)) {
            return [];
        }

        $query = Lesson::query()
            ->where('lessons.status', ActiveStatus::Active->value)
            ->join('lesson_categories', 'lessons.lesson_category_id', '=', 'lesson_categories.id')
            ->where('lesson_categories.status', ActiveStatus::Active->value)
            ->where('lesson_categories.pillar', $pillar)
            ->whereIn('lesson_categories.key', $categoryKeys)
            ->with(['category', 'videos' => fn($q) => $q->orderBy('sort_order', 'asc')]);

        if ($ageGroupId) {
            $query->where(function ($q) use ($ageGroupId) {
                $q->where('lessons.age_group_id', $ageGroupId)
                  ->orWhereNull('lessons.age_group_id');
            });
        }

        $maxLessons = (int) config('assessment.max_recommendation_lessons', 6);
        $lessons = $query
            ->select('lessons.*')
            ->orderBy('lessons.sort_order', 'asc')
            ->orderBy('lessons.id', 'desc')
            ->limit($maxLessons)
            ->get();

        return $lessons->map(function (Lesson $item) use ($isVip) {
            $isLocked = false;
            if (!$isVip && ($item->access_type ?? LessonAccessType::Free) === LessonAccessType::Vip) {
                $isLocked = true;
            }

            return [
                'id' => $item->id,
                'name' => $item->name,
                'slug' => $item->slug,
                'category_id' => $item->lesson_category_id,
                'category_key' => $item->category?->key,
                'category_name' => $item->category?->name,
                'pillar' => $item->category?->pillar,
                'difficulty' => is_object($item->difficulty) ? $item->difficulty->value : $item->difficulty,
                'access_type' => is_object($item->access_type) ? $item->access_type->value : $item->access_type,
                'thumbnail_url' => $item->thumbnail_url,
                'view_count' => (int) $item->view_count,
                'video_count' => (int) $item->video_count,
                'is_locked' => $isLocked,
            ];
        })->toArray();
    }

    /**
     * Xác định age_group_id dựa trên ngày sinh hoặc trạng thái thai nhi của trẻ
     */
    protected function resolveChildAgeGroupId(?Child $child): ?int
    {
        if (!$child) {
            return null;
        }

        try {
            if ($child->is_born == BornStatus::Unborn) {
                return AgeGroup::where('is_prenatal', true)->value('id');
            }

            if ($child->birthday) {
                $months = Carbon::parse($child->birthday)->diffInMonths(now());
                $group = AgeGroup::where('status', ActiveStatus::Active->value)
                    ->where('is_prenatal', false)
                    ->where(function ($q) use ($months) {
                        $q->where('min_months', '<=', $months)
                          ->where('max_months', '>=', $months);
                    })
                    ->first();

                if ($group) {
                    return $group->id;
                }
            }
        } catch (\Throwable $e) {
            // Không chặn nếu tính tuổi gặp lỗi
        }

        return null;
    }

    /**
     * Kiểm tra user hiện tại có gói VIP hoạt động không
     */
    protected function isVipUser(): bool
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
}
