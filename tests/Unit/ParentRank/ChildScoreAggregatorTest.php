<?php

namespace Tests\Unit\ParentRank;

use App\Services\ChildScore\ChildScoreAggregator;
use PHPUnit\Framework\TestCase;

class ChildScoreAggregatorTest extends TestCase
{
    private ChildScoreAggregator $aggregator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->aggregator = new ChildScoreAggregator();
    }

    /**
     * Kiểm tra hàm chuẩn hóa điểm (normalizeScore) về thang điểm 0 - 100
     */
    public function test_normalize_score_scales(): void
    {
        // Thang điểm hệ thống 0-10 -> chuẩn hóa 0-100 (nhân 10)
        $this->assertSame(85.0, $this->aggregator->normalize('iq', 8.5));
        $this->assertSame(70.0, $this->aggregator->normalize('eq', 7.0));
        $this->assertSame(92.0, $this->aggregator->normalize('aq', 9.2));
        $this->assertSame(65.0, $this->aggregator->normalize('pq', 6.5));
        $this->assertSame(80.0, $this->aggregator->normalize('gpa', 8.0));

        // Điểm null hoặc âm/vượt trần
        $this->assertNull($this->aggregator->normalize('iq', null));
        $this->assertSame(0.0, $this->aggregator->normalize('gpa', 0.0));
        $this->assertSame(100.0, $this->aggregator->normalize('gpa', 10.0));
    }
}
