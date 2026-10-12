<?php

namespace Tests\Unit\Assessment;

use App\Services\Assessment\AssessmentBandResolver;
use Tests\TestCase;

class AssessmentBandResolverTest extends TestCase
{
    public function test_aq_band_resolutions(): void
    {
        // <= 3
        $b1 = AssessmentBandResolver::resolve('aq', 2.4);
        $this->assertEquals('B1', $b1['band']);
        $this->assertEquals('Quitter', $b1['label']);
        $this->assertStringContainsString('nhiệm vụ thật nhỏ', $b1['parent_action']);

        // <= 5
        $b2 = AssessmentBandResolver::resolve('aq', 5.0);
        $this->assertEquals('B2', $b2['band']);
        $this->assertEquals('Quitter', $b2['label']);
        $this->assertStringContainsString('việc hơi khó hơn', $b2['parent_action']);

        // <= 7
        $b3 = AssessmentBandResolver::resolve('aq', 6.8);
        $this->assertEquals('B3', $b3['band']);
        $this->assertEquals('Camper', $b3['label']);
        $this->assertStringContainsString('kiên trì đến cùng', $b3['parent_action']);

        // <= 9
        $b4 = AssessmentBandResolver::resolve('aq', 8.5);
        $this->assertEquals('B4', $b4['band']);
        $this->assertEquals('Climber', $b4['label']);
        $this->assertStringContainsString('thử thách mới', $b4['parent_action']);

        // > 9
        $b5 = AssessmentBandResolver::resolve('aq', 9.5);
        $this->assertEquals('B5', $b5['band']);
        $this->assertEquals('Climber', $b5['label']);
        $this->assertStringContainsString('mục tiêu có ý nghĩa', $b5['parent_action']);
    }

    public function test_eq_band_resolutions(): void
    {
        $b1 = AssessmentBandResolver::resolve('eq', 3.0);
        $this->assertEquals('B1', $b1['band']);
        $this->assertEquals('Tiêu cực', $b1['label']);
        $this->assertStringContainsString('gọi tên cảm xúc', $b1['parent_action']);

        $b2 = AssessmentBandResolver::resolve('eq', 4.5);
        $this->assertEquals('B2', $b2['band']);
        $this->assertEquals('Tiêu cực ẩn', $b2['label']);
        $this->assertStringContainsString('không gian an toàn', $b2['parent_action']);

        $b3 = AssessmentBandResolver::resolve('eq', 7.0);
        $this->assertEquals('B3', $b3['band']);
        $this->assertEquals('Trung tính', $b3['label']);
        $this->assertStringContainsString('nói ra cảm xúc', $b3['parent_action']);

        $b4 = AssessmentBandResolver::resolve('eq', 8.0);
        $this->assertEquals('B4', $b4['band']);
        $this->assertEquals('Tích cực', $b4['label']);
        $this->assertStringContainsString('giải quyết mâu thuẫn', $b4['parent_action']);

        $b5 = AssessmentBandResolver::resolve('eq', 10.0);
        $this->assertEquals('B5', $b5['band']);
        $this->assertEquals('Rất tích cực', $b5['label']);
        $this->assertStringContainsString('đồng cảm, hòa giải', $b5['parent_action']);
    }

    public function test_iq_band_resolutions(): void
    {
        $b1 = AssessmentBandResolver::resolve('iq', 2.0);
        $this->assertEquals('B1', $b1['band']);
        $this->assertEquals('Kém', $b1['label']);
        $this->assertStringContainsString('trò chơi đơn giản', $b1['parent_action']);

        $b2 = AssessmentBandResolver::resolve('iq', 5.0);
        $this->assertEquals('B2', $b2['band']);
        $this->assertEquals('Trung bình', $b2['label']);
        $this->assertStringContainsString('10–15 phút mỗi ngày', $b2['parent_action']);

        $b3 = AssessmentBandResolver::resolve('iq', 7.0);
        $this->assertEquals('B3', $b3['band']);
        $this->assertEquals('Khá cao', $b3['label']);

        $b4 = AssessmentBandResolver::resolve('iq', 9.0);
        $this->assertEquals('B4', $b4['band']);
        $this->assertEquals('Rất cao', $b4['label']);

        $b5 = AssessmentBandResolver::resolve('iq', 10.0);
        $this->assertEquals('B5', $b5['band']);
        $this->assertEquals('Xuất sắc', $b5['label']);
    }
}
