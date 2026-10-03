<?php

namespace Tests\Unit\ReportCard;

use App\Services\ReportCard\Support\Score;
use PHPUnit\Framework\TestCase;

class ScoreTest extends TestCase
{
    public function test_full_year_rounding_cases_from_spec(): void
    {
        // (8.0 + 2×7.0)/3 = 7.333... -> 7.3
        $this->assertSame(7.3, Score::formatFullYear(800, 700));

        // (6.5 + 2×6.5)/3 = 6.5 -> 6.5
        $this->assertSame(6.5, Score::formatFullYear(650, 650));

        // (7.0 + 2×7.25)/3 = 7.1666... -> 7.2
        $this->assertSame(7.2, Score::formatFullYear(700, 725));

        // (3.4 + 2×3.55)/3 = 3.5 -> 3.5
        $this->assertSame(3.5, Score::formatFullYear(340, 355));
    }

    public function test_edge_scores_zero_and_ten(): void
    {
        $this->assertSame(0.0, Score::formatFullYear(0, 0));
        $this->assertSame(10.0, Score::formatFullYear(1000, 1000));
    }

    public function test_parsing_different_formats(): void
    {
        $this->assertSame(700, Score::toHundredths('7'));
        $this->assertSame(750, Score::toHundredths('7.50'));
        $this->assertSame(725, Score::toHundredths(7.25));
        $this->assertNull(Score::toHundredths(null));
        $this->assertNull(Score::toHundredths(''));
    }

    public function test_average_calculation(): void
    {
        $this->assertSame(8.25, Score::average([8.0, 8.5]));
        $this->assertSame(8.33, Score::average([8.0, 8.0, 9.0], 2));
        $this->assertNull(Score::average([]));
    }
}
