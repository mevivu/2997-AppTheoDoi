<?php

namespace Tests\Unit\ParentRank;

use App\Services\ChildScore\ChildScoreAggregatorInterface;
use App\Services\ParentRank\ParentRankService;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class ParentRankSuggestionConfigTest extends TestCase
{
    private function makeService(array $analysis = []): ParentRankService
    {
        $aggregator = new class($analysis) implements ChildScoreAggregatorInterface {
            public ?float $receivedThreshold = null;

            public function __construct(private array $analysis) {}

            public function getLatestScores(int $childId): array { return []; }
            public function normalize(string $type, ?float $raw): ?float { return $raw; }
            public function getAverageNormalizedScoreForUser(int $userId): float { return 0.0; }

            public function getChildrenCompetencyAnalysis(int $userId, float $weakThreshold = 5.0): array
            {
                $this->receivedThreshold = $weakThreshold;
                return $this->analysis;
            }
        };

        return new ParentRankService($aggregator);
    }

    private function call(ParentRankService $service, string $method, array $args)
    {
        $ref = new ReflectionMethod($service, $method);
        $ref->setAccessible(true);
        return $ref->invokeArgs($service, $args);
    }

    private function child(string $name, array $weak, float $min, float $avg): array
    {
        return [
            'child_id' => 1,
            'child_fullname' => $name,
            'child_name' => $name,
            'average' => $avg,
            'min_score' => $min,
            'weak_competencies' => $weak,
        ];
    }

    /**
     * Chưa cấu hình gì -> dùng đúng hành vi mặc định
     */
    public function test_default_suggestion_config(): void
    {
        $cfg = $this->call($this->makeService(), 'buildSuggestionConfig', [[]]);

        $this->assertSame(0.5, $cfg['warning_ratio']);
        $this->assertSame(5.0, $cfg['competency_threshold']);
        $this->assertSame(2, $cfg['competency_max_display']);
        $this->assertSame(
            ['usage' => true, 'assessment' => true, 'content' => true, 'competency' => true],
            $cfg['enabled']
        );
        $this->assertSame(ParentRankService::DEFAULT_SUGGESTION_TEXTS['usage'], $cfg['texts']['usage']);
    }

    /**
     * Giá trị admin nhập sai/ngoài biên được kẹp lại, câu để trống dùng mặc định
     */
    public function test_suggestion_config_is_clamped_and_falls_back(): void
    {
        $cfg = $this->call($this->makeService(), 'buildSuggestionConfig', [[
            'parent_rank_warning_ratio' => '250',
            'parent_rank_competency_threshold' => '-3',
            'parent_rank_competency_max_display' => '99',
            'parent_rank_suggest_enable_content' => '0',
            'parent_rank_suggest_text_usage' => '   ',
            'parent_rank_suggest_text_assessment' => 'Câu tuỳ chỉnh',
        ]]);

        $this->assertSame(1.0, $cfg['warning_ratio']);
        $this->assertSame(0.0, $cfg['competency_threshold']);
        $this->assertSame(5, $cfg['competency_max_display']);
        $this->assertFalse($cfg['enabled']['content']);
        $this->assertTrue($cfg['enabled']['usage']);
        $this->assertSame(ParentRankService::DEFAULT_SUGGESTION_TEXTS['usage'], $cfg['texts']['usage']);
        $this->assertSame('Câu tuỳ chỉnh', $cfg['texts']['assessment']);
    }

    /**
     * Ví dụ của anh: EQ = 2, GPA = 3 (lấy các lĩnh vực thấp nhất, tối đa 2) kèm tên bé
     */
    public function test_competency_suggestion_lists_lowest_with_child_name(): void
    {
        $service = $this->makeService([
            $this->child('An', [
                ['code' => 'EQ', 'score' => 2.0],
                ['code' => 'GPA', 'score' => 3.0],
                ['code' => 'AQ', 'score' => 4.0],
            ], 2.0, 4.5),
        ]);

        $msg = $this->call($service, 'buildCompetencySuggestion', [
            1, 5.0, 2, ParentRankService::DEFAULT_SUGGESTION_TEXTS['competency'],
        ]);

        $this->assertSame('Bé An cần tiếp tục luyện tập để cải thiện các chỉ số: EQ = 2, GPA = 3.', $msg);
    }

    /**
     * Chỉ 1 lĩnh vực -> "chỉ số"; điểm lẻ giữ 1 chữ số thập phân; thiếu tên -> "Con"
     */
    public function test_competency_suggestion_single_item_and_missing_name(): void
    {
        $service = $this->makeService([
            $this->child('', [['code' => 'PQ', 'score' => 2.5]], 2.5, 7.0),
        ]);

        $msg = $this->call($service, 'buildCompetencySuggestion', [
            1, 5.0, 2, ParentRankService::DEFAULT_SUGGESTION_TEXTS['competency'],
        ]);

        $this->assertSame('Con cần tiếp tục luyện tập để cải thiện chỉ số: PQ = 2.5.', $msg);
    }

    /**
     * Nhiều bé: chọn bé có chỉ số thấp nhất; max_display và ngưỡng truyền xuống aggregator
     */
    public function test_competency_suggestion_picks_weakest_child_and_respects_settings(): void
    {
        $service = $this->makeService([
            $this->child('Bình', [['code' => 'IQ', 'score' => 4.0]], 4.0, 6.0),
            $this->child('Chi', [
                ['code' => 'EQ', 'score' => 1.0],
                ['code' => 'AQ', 'score' => 3.0],
                ['code' => 'GPA', 'score' => 3.5],
            ], 1.0, 5.0),
        ]);

        $msg = $this->call($service, 'buildCompetencySuggestion', [1, 6.5, 3, '{child}: {list}']);

        $this->assertSame('Bé Chi: EQ = 1, AQ = 3, GPA = 3.5', $msg);
    }

    /**
     * Không có bé nào dưới ngưỡng -> không có câu gợi ý năng lực
     */
    public function test_competency_suggestion_returns_null_when_all_good(): void
    {
        $service = $this->makeService([
            $this->child('An', [], 8.0, 8.5),
        ]);

        $this->assertNull($this->call($service, 'buildCompetencySuggestion', [
            1, 5.0, 2, ParentRankService::DEFAULT_SUGGESTION_TEXTS['competency'],
        ]));
    }
}
