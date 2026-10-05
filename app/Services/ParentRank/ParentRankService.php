<?php

namespace App\Services\ParentRank;

use App\Enums\Question\QuestionType;
use App\Enums\User\ParentRank;
use App\Enums\VerifiedStatus;
use App\Models\Child;
use App\Models\ChildEvaluation;
use App\Models\FeatureUsage;
use App\Models\ParentRankSnapshot;
use App\Models\Rating;
use App\Models\RatingPQ;
use App\Models\User;
use App\Models\UserDailyActivity;
use App\Services\ChildScore\ChildScoreAggregatorInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class ParentRankService implements ParentRankServiceInterface
{
    protected ChildScoreAggregatorInterface $childScoreAggregator;

    public function __construct(ChildScoreAggregatorInterface $childScoreAggregator)
    {
        $this->childScoreAggregator = $childScoreAggregator;
    }

    /**
     * Lấy cấu hình phân hạng phụ huynh từ CSDL
     */
    public function getConfig(): array
    {
        $keys = [
            'parent_rank_active',
            'parent_rank_weight_usage',
            'parent_rank_weight_frequency',
            'parent_rank_weight_assessment',
            'parent_rank_weight_child_score',
            'parent_rank_target_usage_minutes',
            'parent_rank_target_active_days',
            'parent_rank_target_assessments',
            'parent_rank_target_lesson_video_views',
            'parent_rank_points_c',
            'parent_rank_points_b',
            'parent_rank_points_a',
            'parent_rank_points_bronze',
            'parent_rank_points_silver',
            'parent_rank_points_gold',
            'parent_rank_points_diamond',
        ];

        $settings = DB::table('settings')->whereIn('setting_key', $keys)->pluck('plain_value', 'setting_key');

        return [
            'active' => ($settings['parent_rank_active'] ?? '1') === '1',
            'weights' => [
                'usage' => (float) ($settings['parent_rank_weight_usage'] ?? 30),
                'frequency' => (float) ($settings['parent_rank_weight_frequency'] ?? 25),
                'assessment' => (float) ($settings['parent_rank_weight_assessment'] ?? 25),
                'child_score' => (float) ($settings['parent_rank_weight_child_score'] ?? 20),
            ],
            'targets' => [
                'usage_minutes' => (int) ($settings['parent_rank_target_usage_minutes'] ?? 600),
                'active_days' => (int) ($settings['parent_rank_target_active_days'] ?? 20),
                'assessments' => (int) ($settings['parent_rank_target_assessments'] ?? 5),
                'lesson_video_views' => (int) ($settings['parent_rank_target_lesson_video_views'] ?? 20),
            ],
            'thresholds' => [
                'c' => (float) ($settings['parent_rank_points_c'] ?? $settings['parent_rank_points_silver'] ?? 40),
                'b' => (float) ($settings['parent_rank_points_b'] ?? $settings['parent_rank_points_gold'] ?? 60),
                'a' => (float) ($settings['parent_rank_points_a'] ?? $settings['parent_rank_points_diamond'] ?? 80),
            ],
        ];
    }

    /**
     * Thu thập các chỉ số thực tế trong tháng của một phụ huynh
     */
    public function collectMetrics(User $user, string $period): array
    {
        $startDate = Carbon::parse($period . '-01 00:00:00', 'Asia/Ho_Chi_Minh');
        $endDate = $startDate->copy()->endOfMonth();

        // 1. Chỉ số thời gian dùng app và tần suất từ user_daily_activities
        $activityStats = UserDailyActivity::where('user_id', $user->id)
            ->whereBetween('activity_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('SUM(active_seconds) as total_seconds, COUNT(DISTINCT activity_date) as active_days, SUM(session_count) as total_sessions')
            ->first();

        $usageMinutes = (int) round(($activityStats->total_seconds ?? 0) / 60);
        $activeDays = (int) ($activityStats->active_days ?? 0);
        $sessionCount = (int) ($activityStats->total_sessions ?? 0);

        // 2. Chỉ số số bài đánh giá thực hiện cho con
        $assessmentCount = $this->countAssessmentsInMonth($user->id, $startDate, $endDate);

        // 3. Chỉ số điểm phát triển trung bình của con
        $childScoreAvg = $this->childScoreAggregator->getAverageNormalizedScoreForUser($user->id);

        // 4. Số lần xem bài học và video giáo dục trong tháng
        $lessonVideoViews = FeatureUsage::where('user_id', $user->id)
            ->where(function ($q) {
                $q->whereIn('feature_code', ['lesson_education', 'video_education'])
                  ->orWhereIn('action', ['watch_lesson', 'watch_video']);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        return [
            'usage_minutes' => $usageMinutes,
            'active_days' => $activeDays,
            'session_count' => $sessionCount,
            'assessment_count' => $assessmentCount,
            'child_score_avg' => $childScoreAvg,
            'lesson_video_views' => $lessonVideoViews,
        ];
    }

    /**
     * Đếm số bài đánh giá thực tế đã làm cho các con trong khoảng thời gian (loại trừ các bản ghi rỗng tạo sẵn)
     */
    protected function countAssessmentsInMonth(int $userId, Carbon $startDate, Carbon $endDate): int
    {
        $childrenIds = Child::where('user_id', $userId)->pluck('id')->toArray();
        if (empty($childrenIds)) {
            return 0;
        }

        // IQ: Phải có điểm và trạng thái Active, cập nhật trong tháng
        $iqCount = Rating::whereIn('child_id', $childrenIds)
            ->where('type', QuestionType::IQ)
            ->whereNotNull('score')
            ->where('status', VerifiedStatus::Active)
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();

        // EQ & AQ: Phải có điểm, tạo trong tháng
        $eqAqCount = Rating::whereIn('child_id', $childrenIds)
            ->whereIn('type', [QuestionType::EQ, QuestionType::AQ])
            ->whereNotNull('score')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // PQ: Đánh giá thể chất tạo trong tháng
        $pqCount = RatingPQ::whereIn('child_id', $childrenIds)
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('assessment_date', [$startDate->toDateString(), $endDate->toDateString()])
                  ->orWhereBetween('created_at', [$startDate, $endDate]);
            })
            ->count();

        // GPA: Bản ghi đánh giá học bạ điện tử trong tháng
        $gpaCount = ChildEvaluation::whereHas('classGrade', function ($q) use ($childrenIds) {
            $q->whereIn('child_id', $childrenIds);
        })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        return $iqCount + $eqAqCount + $pqCount + $gpaCount;
    }

    /**
     * Tính toán điểm thành phần, tổng điểm và cấp bậc đạt được
     */
    public function computeScores(array $metrics, ?array $config = null): array
    {
        $config = $config ?? $this->getConfig();
        $targets = $config['targets'];
        $weights = $config['weights'];

        $targetUsage = max(1, (int) ($targets['usage_minutes'] ?? 600));
        $targetDays = max(1, (int) ($targets['active_days'] ?? 20));
        $targetAssessments = max(1, (int) ($targets['assessments'] ?? 5));

        // Chuẩn hóa điểm thành phần về thang 0 - 100
        $scoreUsage = min(100.0, round(((float) $metrics['usage_minutes'] / $targetUsage) * 100, 2));
        $scoreFrequency = min(100.0, round(((float) $metrics['active_days'] / $targetDays) * 100, 2));
        $scoreAssessment = min(100.0, round(((float) $metrics['assessment_count'] / $targetAssessments) * 100, 2));
        $scoreChild = min(100.0, max(0.0, (float) ($metrics['child_score_avg'] ?? 0)));

        $totalWeight = array_sum($weights);
        if ($totalWeight <= 0) {
            $totalWeight = 100.0;
        }

        $totalPoints = (
            ($scoreUsage * $weights['usage']) +
            ($scoreFrequency * $weights['frequency']) +
            ($scoreAssessment * $weights['assessment']) +
            ($scoreChild * $weights['child_score'])
        ) / $totalWeight;

        $totalPoints = round($totalPoints, 2);
        $rank = $this->resolveRank($totalPoints, $config);

        return [
            'score_usage' => $scoreUsage,
            'score_frequency' => $scoreFrequency,
            'score_assessment' => $scoreAssessment,
            'score_child' => $scoreChild,
            'total_points' => $totalPoints,
            'rank' => $rank,
        ];
    }

    /**
     * Xác định cấp bậc phụ huynh dựa trên tổng điểm đạt được và cấu hình
     */
    public function resolveRank(float $totalPoints, ?array $config = null): ParentRank
    {
        $config = $config ?? $this->getConfig();
        $th = $config['thresholds'];

        if ($totalPoints >= ($th['a'] ?? 80)) {
            return ParentRank::A;
        }
        if ($totalPoints >= ($th['b'] ?? 60)) {
            return ParentRank::B;
        }
        if ($totalPoints >= ($th['c'] ?? 40)) {
            return ParentRank::C;
        }

        return ParentRank::D;
    }

    /**
     * Tính toán và lưu bản chụp xếp hạng của phụ huynh cho kỳ cụ thể
     */
    public function calculateAndSave(User $user, string $period, bool $finalize = false): ParentRankSnapshot
    {
        $config = $this->getConfig();
        $metrics = $this->collectMetrics($user, $period);
        $scores = $this->computeScores($metrics, $config);

        // Lưu hoặc cập nhật snapshot
        $snapshot = ParentRankSnapshot::updateOrCreate(
            [
                'user_id' => $user->id,
                'period' => $period,
            ],
            [
                'usage_minutes' => $metrics['usage_minutes'],
                'active_days' => $metrics['active_days'],
                'session_count' => $metrics['session_count'],
                'assessment_count' => $metrics['assessment_count'],
                'lesson_video_views' => $metrics['lesson_video_views'] ?? 0,
                'child_score_avg' => $metrics['child_score_avg'],
                'score_usage' => $scores['score_usage'],
                'score_frequency' => $scores['score_frequency'],
                'score_assessment' => $scores['score_assessment'],
                'score_child' => $scores['score_child'],
                'total_points' => $scores['total_points'],
                'rank' => $scores['rank'],
                'is_final' => $finalize,
                'config_snapshot' => $config,
                'calculated_at' => now(),
            ]
        );

        // Nếu kỳ đang tính là kỳ hiện tại hoặc người dùng chưa có hạng kỳ này, cập nhật vào bảng users
        $currentPeriod = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m');
        if ($period === $currentPeriod || empty($user->parent_rank_period) || $period >= $user->parent_rank_period) {
            $user->update([
                'parent_rank' => $scores['rank'],
                'parent_rank_points' => $scores['total_points'],
                'parent_rank_period' => $period,
                'parent_rank_updated_at' => now(),
            ]);
        }

        return $snapshot;
    }

    /**
     * Tính toán xếp hạng cho toàn bộ người dùng trong kỳ
     */
    public function calculateAll(string $period, bool $finalize = false, ?callable $onProgress = null): int
    {
        // Quét tất cả người dùng hoạt động hoặc có con
        $query = User::query();

        $totalUsers = $query->count();
        if ($totalUsers === 0) {
            return 0;
        }

        $processedCount = 0;

        $query->chunk(200, function ($users) use ($period, $finalize, &$processedCount, $onProgress) {
            foreach ($users as $user) {
                try {
                    $this->calculateAndSave($user, $period, $finalize);
                    $processedCount++;
                    if ($onProgress) {
                        $onProgress($user, $processedCount);
                    }
                } catch (Throwable $e) {
                    \Illuminate\Support\Facades\Log::error("Lỗi tính thứ hạng phụ huynh user {$user->id}: " . $e->getMessage());
                }
            }
        });

        // Cập nhật thứ hạng vị trí (position) trên bảng xếp hạng kỳ này
        $snapshots = ParentRankSnapshot::where('period', $period)
            ->orderByDesc('total_points')
            ->orderBy('id')
            ->get();

        $position = 1;
        foreach ($snapshots as $snap) {
            $snap->position = $position++;
            $snap->save();
        }

        return $processedCount;
    }

    /**
     * Lấy thông tin tiến độ và hạng của người dùng kèm hướng dẫn phấn đấu
     */
    public function getUserProgress(User $user): array
    {
        $currentPeriod = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m');
        $config = $this->getConfig();

        // 1. Tìm snapshot kỳ hiện tại, nếu chưa có thì tính ngay on-the-fly
        $snapshot = ParentRankSnapshot::where('user_id', $user->id)
            ->where('period', $currentPeriod)
            ->first();

        if (!$snapshot) {
            $snapshot = $this->calculateAndSave($user, $currentPeriod, false);
        } elseif (!$snapshot->is_final && (!$snapshot->calculated_at || $snapshot->calculated_at->diffInMinutes(now()) >= 10)) {
            $snapshot = $this->calculateAndSave($user, $currentPeriod, false);
        }

        $rank = $snapshot->rank instanceof ParentRank ? $snapshot->rank : ParentRank::from((int) $snapshot->rank);
        $nextRank = $rank->getNextRank();
        $th = $config['thresholds'];

        $minPointsCurrent = match ($rank) {
            ParentRank::D => 0.0,
            ParentRank::C => (float) ($th['c'] ?? 40),
            ParentRank::B => (float) ($th['b'] ?? 60),
            ParentRank::A => (float) ($th['a'] ?? 80),
        };

        $minPointsNext = null;
        $pointsNeeded = 0.0;
        if ($nextRank) {
            $minPointsNext = match ($nextRank) {
                ParentRank::C => (float) ($th['c'] ?? 40),
                ParentRank::B => (float) ($th['b'] ?? 60),
                ParentRank::A => (float) ($th['a'] ?? 80),
                default => 100.0,
            };
            $pointsNeeded = max(0.0, round($minPointsNext - $snapshot->total_points, 2));
        }

        $progressPercent = 100.0;
        if ($nextRank && $minPointsNext !== null && ($minPointsNext > $minPointsCurrent)) {
            $progressPercent = round(
                max(0.0, min(100.0, (($snapshot->total_points - $minPointsCurrent) / ($minPointsNext - $minPointsCurrent)) * 100)),
                1
            );
        }

        $targets = $config['targets'];
        $weights = $config['weights'];

        $breakdown = [
            [
                'key' => 'usage',
                'label' => 'Thời gian sử dụng',
                'raw' => (int) $snapshot->usage_minutes,
                'unit' => 'phút',
                'target' => (int) ($targets['usage_minutes'] ?? 600),
                'score' => (float) $snapshot->score_usage,
                'weight' => (float) ($weights['usage'] ?? 30),
            ],
            [
                'key' => 'frequency',
                'label' => 'Số ngày vào app',
                'raw' => (int) $snapshot->active_days,
                'unit' => 'ngày',
                'target' => (int) ($targets['active_days'] ?? 20),
                'score' => (float) $snapshot->score_frequency,
                'weight' => (float) ($weights['frequency'] ?? 25),
            ],
            [
                'key' => 'assessment',
                'label' => 'Bài đánh giá cho con',
                'raw' => (int) $snapshot->assessment_count,
                'unit' => 'bài',
                'target' => (int) ($targets['assessments'] ?? 5),
                'score' => (float) $snapshot->score_assessment,
                'weight' => (float) ($weights['assessment'] ?? 25),
            ],
            [
                'key' => 'child_score',
                'label' => 'Chỉ số phát triển của con',
                'raw' => (float) $snapshot->child_score_avg,
                'unit' => 'điểm',
                'target' => 100,
                'score' => (float) $snapshot->score_child,
                'weight' => (float) ($weights['child_score'] ?? 20),
            ],
        ];

        // 5 tiêu chí cảnh báo mức độ đồng hành (< 50% tiêu chuẩn)
        $targetUsage = max(1, (int) ($targets['usage_minutes'] ?? 600));
        $targetDays = max(1, (int) ($targets['active_days'] ?? 20));
        $targetAssessments = max(1, (int) ($targets['assessments'] ?? 5));
        $targetViews = max(1, (int) ($targets['lesson_video_views'] ?? 20));

        $usageMinutes = (int) $snapshot->usage_minutes;
        $activeDays = (int) $snapshot->active_days;
        $assessmentCount = (int) $snapshot->assessment_count;
        $lessonVideoViews = (int) ($snapshot->lesson_video_views ?? 0);

        $suggestions = [];

        // Tiêu chí 1 & 2: Thời gian sử dụng app/tháng HOẶC Số ngày đăng nhập/tháng < 50%
        if ($usageMinutes < ($targetUsage * 0.5) || $activeDays < ($targetDays * 0.5)) {
            $suggestions[] = 'Bố mẹ nên dành thêm thời gian kiểm tra các chỉ số và năng lực của con thường xuyên để theo dõi sự phát triển.';
        }

        // Tiêu chí 3: Số bài đánh giá/tháng < 50%
        if ($assessmentCount < ($targetAssessments * 0.5)) {
            $suggestions[] = 'Bố mẹ nên cập nhật chiều cao và cân nặng cho con hàng tháng và các chỉ số IQ, AQ, EQ, học bạ điện tử định kỳ để theo dõi tăng trưởng chính xác hơn.';
        }

        // Tiêu chí 4: Số lần xem bài học/video/tháng < 50%
        if ($lessonVideoViews < ($targetViews * 0.5)) {
            $suggestions[] = 'Bố mẹ nên cho con luyện tập thêm các bài học và bài tập trong ứng dụng để phát triển các năng lực.';
        }

        // Tiêu chí 5: Điểm trung bình các năng lực
        // Trường hợp có >1 con thì lấy con nào <5 điểm cảnh báo, nhiều con cùng <5 thì cảnh báo min(con 1,2,3..)
        $childrenAnalysis = $this->childScoreAggregator->getChildrenCompetencyAnalysis($user->id);
        $childrenUnder5 = array_values(array_filter($childrenAnalysis, function ($item) {
            return $item['average'] !== null && $item['average'] < 5.0;
        }));

        if (!empty($childrenUnder5)) {
            usort($childrenUnder5, fn($a, $b) => $a['average'] <=> $b['average']);
            $lowestChild = $childrenUnder5[0];

            $msg = 'Con và bố mẹ cần tiếp tục luyện tập để cải thiện các chỉ số năng lực.';
            if (!empty($lowestChild['lowest_competency'])) {
                $compCode = $lowestChild['lowest_competency']['code'];
                $compRank = $lowestChild['lowest_competency']['rank'];
                if ($compRank > 1) {
                    $targetRank = $compRank - 1;
                    $msg .= " (ví dụ: {$compCode} #{$compRank} → #{$targetRank})";
                }
            }
            $suggestions[] = $msg;
        }

        // Nếu tất cả các chỉ số đều đạt (không có cảnh báo < 50%), hiển thị câu khích lệ theo hạng hiện tại
        if (empty($suggestions)) {
            $suggestions[] = $rank->description();
        }

        return [
            'period' => $snapshot->period,
            'is_final' => (bool) $snapshot->is_final,
            'rank' => [
                'value' => $rank->value,
                'name' => $rank->name(),
                'short_name' => $rank->shortName(),
                'description' => $rank->description(),
                'color' => $rank->colorHex(),
                'icon' => $rank->icon(),
                'badge' => $rank->badge(),
                'min_points' => $minPointsCurrent,
            ],
            'total_points' => (float) $snapshot->total_points,
            'position' => $snapshot->position,
            'progress_percent' => $progressPercent,
            'next_rank' => $nextRank ? [
                'value' => $nextRank->value,
                'name' => $nextRank->name(),
                'short_name' => $nextRank->shortName(),
                'description' => $nextRank->description(),
                'color' => $nextRank->colorHex(),
                'icon' => $nextRank->icon(),
                'min_points' => $minPointsNext,
                'points_needed' => $pointsNeeded,
            ] : null,
            'suggestions' => $suggestions,
            'suggestion' => implode("\n\n", $suggestions),
            'breakdown' => $breakdown,
            'calculated_at' => $snapshot->calculated_at ? $snapshot->calculated_at->toDateTimeString() : null,
        ];
    }
}
