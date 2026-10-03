<?php

namespace Tests\Unit\ReportCard;

use App\Services\ReportCard\Dto\ReportCardInput;
use App\Services\ReportCard\Dto\SubjectInput;
use App\Services\ReportCard\PrimaryCalculator;
use PHPUnit\Framework\TestCase;

class PrimaryCalculatorTest extends TestCase
{
    private PrimaryCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new PrimaryCalculator();
    }

    private function createStandardPrimarySubjects(
        string $defaultLevel = 'excellent',
        float $defaultTestScore = 9.5
    ): array {
        // Lớp 1-2 standard: 2 level_with_score (Toán, Tiếng Việt), 6 level (Đạo đức, TN&XH, GDTC, Âm nhạc, Mĩ thuật, HĐTN)
        return [
            new SubjectInput(2, 'Toán', 'level_with_score', true, 1, hk1Grade: $defaultTestScore, hk1Level: $defaultLevel, grade: $defaultTestScore, achievementLevel: $defaultLevel),
            new SubjectInput(12, 'Tiếng Việt', 'level_with_score', true, 2, hk1Grade: $defaultTestScore, hk1Level: $defaultLevel, grade: $defaultTestScore, achievementLevel: $defaultLevel),
            new SubjectInput(4, 'Đạo đức', 'level', true, 3, hk1Level: $defaultLevel, achievementLevel: $defaultLevel),
            new SubjectInput(5, 'Tự nhiên và Xã hội', 'level', true, 4, hk1Level: $defaultLevel, achievementLevel: $defaultLevel),
            new SubjectInput(9, 'Giáo dục thể chất', 'level', true, 5, hk1Level: $defaultLevel, achievementLevel: $defaultLevel),
            new SubjectInput(26, 'Âm nhạc', 'level', true, 6, hk1Level: $defaultLevel, achievementLevel: $defaultLevel),
            new SubjectInput(27, 'Mĩ thuật', 'level', true, 7, hk1Level: $defaultLevel, achievementLevel: $defaultLevel),
            new SubjectInput(11, 'Hoạt động trải nghiệm', 'level', true, 8, hk1Level: $defaultLevel, achievementLevel: $defaultLevel),
        ];
    }

    private function createStandardPersonalities(string $defaultStatus = 'good'): array
    {
        $qualities = [
            ['id' => 1, 'name' => 'Chăm học, chăm làm', 'status' => $defaultStatus],
            ['id' => 2, 'name' => 'Tự tin, trách nhiệm', 'status' => $defaultStatus],
            ['id' => 3, 'name' => 'Trung thực, kỉ luật', 'status' => $defaultStatus],
            ['id' => 4, 'name' => 'Yêu thương, đoàn kết', 'status' => $defaultStatus],
        ];

        $capabilities = [
            ['id' => 1, 'name' => 'Tự chủ và tự học', 'status' => $defaultStatus],
            ['id' => 2, 'name' => 'Giao tiếp và hợp tác', 'status' => $defaultStatus],
            ['id' => 3, 'name' => 'Giải quyết vấn đề và sáng tạo', 'status' => $defaultStatus],
        ];

        return [$qualities, $capabilities];
    }

    public function test_all_excellent_and_scores_ge_9_yields_excellent(): void
    {
        $subjects = $this->createStandardPrimarySubjects('excellent', 9.5);
        [$qualities, $capabilities] = $this->createStandardPersonalities('good');

        $input = new ReportCardInput('primary', 1, 'semester_2', $subjects, $qualities, $capabilities);
        $result = $this->calculator->calculate($input);

        $this->assertSame('ok', $result->status);
        $this->assertSame('excellent', $result->rating);
    }

    public function test_test_score_between_7_and_8_point_9_yields_completed_good(): void
    {
        // One test score is 8.0 (< 9.0 but >= 7.0)
        $subjects = $this->createStandardPrimarySubjects('excellent', 9.5);
        $subjects[0]->grade = 8.0;
        $subjects[0]->hk1Grade = 8.0;

        [$qualities, $capabilities] = $this->createStandardPersonalities('good');

        $input = new ReportCardInput('primary', 1, 'semester_2', $subjects, $qualities, $capabilities);
        $result = $this->calculator->calculate($input);

        $this->assertSame('ok', $result->status);
        $this->assertSame('completed_good', $result->rating);
    }

    public function test_one_personality_achieved_drops_to_completed(): void
    {
        $subjects = $this->createStandardPrimarySubjects('excellent', 9.5);
        [$qualities, $capabilities] = $this->createStandardPersonalities('good');
        // Quality 1 is 'achieved' (Đạt) instead of 'good' (Tốt)
        $qualities[0]['status'] = 'achieved';

        $input = new ReportCardInput('primary', 1, 'semester_2', $subjects, $qualities, $capabilities);
        $result = $this->calculator->calculate($input);

        $this->assertSame('ok', $result->status);
        $this->assertSame('completed', $result->rating);
    }

    public function test_one_subject_completed_drops_to_completed(): void
    {
        $subjects = $this->createStandardPrimarySubjects('excellent', 9.5);
        // Art (Mĩ thuật) is completed (Hoàn thành)
        $subjects[6]->achievementLevel = 'completed';
        $subjects[6]->hk1Level = 'completed';

        [$qualities, $capabilities] = $this->createStandardPersonalities('good');

        $input = new ReportCardInput('primary', 1, 'semester_2', $subjects, $qualities, $capabilities);
        $result = $this->calculator->calculate($input);

        $this->assertSame('ok', $result->status);
        $this->assertSame('completed', $result->rating);
    }

    public function test_one_subject_not_completed_yields_not_completed(): void
    {
        $subjects = $this->createStandardPrimarySubjects('excellent', 9.5);
        $subjects[0]->achievementLevel = 'not_completed';
        $subjects[0]->hk1Level = 'not_completed';

        [$qualities, $capabilities] = $this->createStandardPersonalities('good');

        $input = new ReportCardInput('primary', 1, 'semester_2', $subjects, $qualities, $capabilities);
        $result = $this->calculator->calculate($input);

        $this->assertSame('ok', $result->status);
        $this->assertSame('not_completed', $result->rating);
    }

    public function test_invalid_input_when_secondary_values_used_in_primary(): void
    {
        // F5 & Q4: achieved or not_achieved in primary
        $subjects = $this->createStandardPrimarySubjects('excellent', 9.5);
        $subjects[0]->achievementLevel = 'achieved';
        $subjects[0]->hk1Level = 'achieved';

        [$qualities, $capabilities] = $this->createStandardPersonalities('good');

        $input = new ReportCardInput('primary', 1, 'semester_2', $subjects, $qualities, $capabilities);
        $result = $this->calculator->calculate($input);

        $this->assertSame('invalid_input', $result->status);
        $this->assertNotEmpty($result->warnings);
    }

    public function test_missing_periodic_test_score_returns_incomplete(): void
    {
        $subjects = $this->createStandardPrimarySubjects('excellent', 9.5);
        // Math is level_with_score, remove test score
        $subjects[0]->grade = null;
        $subjects[0]->hk1Grade = null;
        $subjects[0]->hk2Grade = null;

        [$qualities, $capabilities] = $this->createStandardPersonalities('good');

        $input = new ReportCardInput('primary', 1, 'semester_1', $subjects, $qualities, $capabilities);
        $result = $this->calculator->calculate($input);

        $this->assertSame('incomplete', $result->status);
        $this->assertNotEmpty($result->missing);
    }

    public function test_semester_1_marked_as_reference(): void
    {
        $subjects = $this->createStandardPrimarySubjects('excellent', 9.5);
        [$qualities, $capabilities] = $this->createStandardPersonalities('good');

        $input = new ReportCardInput('primary', 1, 'semester_1', $subjects, $qualities, $capabilities);
        $result = $this->calculator->calculate($input);

        $this->assertSame('reference', $result->scope);
        $this->assertNotEmpty($result->warnings);
    }
}
