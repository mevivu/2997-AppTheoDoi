<?php

namespace Tests\Unit\ParentRank;

use App\Enums\User\ParentRank;
use App\Services\ChildScore\ChildScoreAggregator;
use App\Services\ParentRank\ParentRankService;
use PHPUnit\Framework\TestCase;

class ParentRankEngineTest extends TestCase
{
    private ParentRankService $service;
    private array $defaultConfig;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new ParentRankService(new ChildScoreAggregator());

        $this->defaultConfig = [
            'is_active' => true,
            'weights' => [
                'usage' => 30,
                'frequency' => 25,
                'assessment' => 25,
                'child_score' => 20,
            ],
            'targets' => [
                'usage_minutes' => 600,
                'active_days' => 20,
                'assessments' => 5,
                'lesson_video_views' => 20,
            ],
            'thresholds' => [
                'c' => 40,
                'b' => 60,
                'a' => 80,
            ],
        ];
    }

    /**
     * Kiểm tra các mốc ngưỡng chuyển hạng của enum và resolveRank
     */
    public function test_resolve_rank_boundaries(): void
    {
        $this->assertSame(ParentRank::D, $this->service->resolveRank(0.0, $this->defaultConfig));
        $this->assertSame(ParentRank::D, $this->service->resolveRank(39.99, $this->defaultConfig));

        $this->assertSame(ParentRank::C, $this->service->resolveRank(40.0, $this->defaultConfig));
        $this->assertSame(ParentRank::C, $this->service->resolveRank(59.99, $this->defaultConfig));

        $this->assertSame(ParentRank::B, $this->service->resolveRank(60.0, $this->defaultConfig));
        $this->assertSame(ParentRank::B, $this->service->resolveRank(79.9, $this->defaultConfig));

        $this->assertSame(ParentRank::A, $this->service->resolveRank(80.0, $this->defaultConfig));
        $this->assertSame(ParentRank::A, $this->service->resolveRank(100.0, $this->defaultConfig));
    }

    /**
     * Trường hợp người dùng không có hoạt động nào (tất cả chỉ số = 0)
     */
    public function test_compute_score_with_zero_metrics(): void
    {
        $metrics = [
            'usage_minutes' => 0,
            'active_days' => 0,
            'session_count' => 0,
            'assessment_count' => 0,
            'child_score_avg' => null,
            'lesson_video_views' => 0,
        ];

        $result = $this->service->computeScores($metrics, $this->defaultConfig);

        $this->assertSame(0.0, $result['score_usage']);
        $this->assertSame(0.0, $result['score_frequency']);
        $this->assertSame(0.0, $result['score_assessment']);
        $this->assertSame(0.0, $result['score_child']);
        $this->assertSame(0.0, $result['total_points']);
        $this->assertSame(ParentRank::D, $result['rank']);
    }

    /**
     * Kiểm tra trường hợp vượt mục tiêu tháng: không được vượt quá 100 điểm thành phần
     */
    public function test_compute_score_capping_at_target(): void
    {
        $metrics = [
            'usage_minutes' => 1200,   // Gấp đôi mục tiêu 600
            'active_days' => 31,       // Vượt mục tiêu 20 ngày
            'session_count' => 50,
            'assessment_count' => 20,  // Gấp 4 lần mục tiêu 5 bài
            'child_score_avg' => 100.0,
            'lesson_video_views' => 50,
        ];

        $result = $this->service->computeScores($metrics, $this->defaultConfig);

        $this->assertSame(100.0, $result['score_usage']);
        $this->assertSame(100.0, $result['score_frequency']);
        $this->assertSame(100.0, $result['score_assessment']);
        $this->assertSame(100.0, $result['score_child']);
        $this->assertSame(100.0, $result['total_points']);
        $this->assertSame(ParentRank::A, $result['rank']);
    }

    /**
     * Kiểm tra tỷ lệ trọng số khi đạt 50% ở tất cả các chỉ số
     */
    public function test_compute_score_weight_proportions(): void
    {
        $metrics = [
            'usage_minutes' => 300,    // 50% của 600 -> điểm 50 -> đóng góp 50 * 0.3 = 15
            'active_days' => 10,       // 50% của 20  -> điểm 50 -> đóng góp 50 * 0.25 = 12.5
            'session_count' => 15,
            'assessment_count' => 2.5, // 50% của 5   -> điểm 50 -> đóng góp 50 * 0.25 = 12.5
            'child_score_avg' => 50.0, // 50 điểm     -> điểm 50 -> đóng góp 50 * 0.2 = 10
            'lesson_video_views' => 10,
        ];

        $result = $this->service->computeScores($metrics, $this->defaultConfig);

        $this->assertSame(50.0, $result['score_usage']);
        $this->assertSame(50.0, $result['score_frequency']);
        $this->assertSame(50.0, $result['score_assessment']);
        $this->assertSame(50.0, $result['score_child']);
        $this->assertEquals(50.0, $result['total_points']);
        $this->assertSame(ParentRank::C, $result['rank']); // 50 điểm đạt Hạng C (40 - <60)
    }

    /**
     * Kiểm tra Enum ParentRank các hàm hỗ trợ
     */
    public function test_parent_rank_enum_methods(): void
    {
        $this->assertSame('Hạng D – Bố mẹ cần hỗ trợ thêm', ParentRank::D->label());
        $this->assertSame('Hạng C – Bố mẹ đang cố gắng', ParentRank::C->label());
        $this->assertSame('Hạng B – Bố mẹ tích cực', ParentRank::B->label());
        $this->assertSame('Hạng A – Bố mẹ gương mẫu', ParentRank::A->label());

        $this->assertSame(0.0, ParentRank::D->defaultMinPoints());
        $this->assertSame(40.0, ParentRank::C->defaultMinPoints());
        $this->assertSame(60.0, ParentRank::B->defaultMinPoints());
        $this->assertSame(80.0, ParentRank::A->defaultMinPoints());

        $this->assertSame(ParentRank::C, ParentRank::D->getNextRank());
        $this->assertSame(ParentRank::B, ParentRank::C->getNextRank());
        $this->assertSame(ParentRank::A, ParentRank::B->getNextRank());
        $this->assertNull(ParentRank::A->getNextRank());
    }
}
