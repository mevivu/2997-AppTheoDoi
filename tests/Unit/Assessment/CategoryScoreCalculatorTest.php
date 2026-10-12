<?php

namespace Tests\Unit\Assessment;

use App\Services\Assessment\CategoryScoreCalculator;
use Tests\TestCase;

class CategoryScoreCalculatorTest extends TestCase
{
    public function test_calculate_iq_categories(): void
    {
        $groupTotals = [
            'linguistic' => 3,
            'logic_math' => 4,
            'visual' => 3,
            'memory' => 3,
        ];

        $groupCorrects = [
            'linguistic' => 1, // 1/3 = 3.3 -> weak
            'logic_math' => 3, // 3/4 = 7.5 -> not weak
            'visual' => 2,     // 2/3 = 6.7 -> not weak
            'memory' => 1,     // 1/3 = 3.3 -> weak
        ];

        $result = CategoryScoreCalculator::calculateIq($groupTotals, $groupCorrects);

        $this->assertCount(4, $result);

        $this->assertEquals(3.3, $result['linguistic']['score']);
        $this->assertTrue($result['linguistic']['is_weak']);
        $this->assertEquals('language', $result['linguistic']['lesson_category_key']);

        $this->assertEquals(7.5, $result['logic_math']['score']);
        $this->assertFalse($result['logic_math']['is_weak']);

        $this->assertEquals(6.7, $result['visual']['score']);
        $this->assertFalse($result['visual']['is_weak']);

        $this->assertEquals(3.3, $result['memory']['score']);
        $this->assertTrue($result['memory']['is_weak']);
    }

    public function test_all_categories_in_config_have_valid_structure(): void
    {
        $categories = config('assessment.categories');
        $this->assertArrayHasKey('iq', $categories);
        $this->assertArrayHasKey('eq', $categories);
        $this->assertArrayHasKey('aq', $categories);

        foreach (['iq', 'eq', 'aq'] as $type) {
            foreach ($categories[$type] as $key => $conf) {
                $this->assertNotEmpty($conf['key']);
                $this->assertNotEmpty($conf['rating_column']);
                $this->assertNotEmpty($conf['group_type']);
                $this->assertNotEmpty($conf['label']);
                $this->assertNotEmpty($conf['lesson_category_key']);
                $this->assertNotEmpty($conf['pillar']);
            }
        }
    }
}
