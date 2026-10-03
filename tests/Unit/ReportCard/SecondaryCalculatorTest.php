<?php

namespace Tests\Unit\ReportCard;

use App\Services\ReportCard\Dto\ReportCardInput;
use App\Services\ReportCard\Dto\SubjectInput;
use App\Services\ReportCard\SecondaryCalculator;
use PHPUnit\Framework\TestCase;

class SecondaryCalculatorTest extends TestCase
{
    private SecondaryCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new SecondaryCalculator();
    }

    private function createStandardSecondarySubjects(
        array $scoreGrades = [],
        array $commentLevels = []
    ): array {
        // Defaults: 7 score subjects (Math, Lit, Lang, Sci, Hist, Tech, Info), 4 comment subjects (PE, Music, Art, Activity)
        $scoreNames = [
            2 => 'Toán',
            13 => 'Ngữ văn',
            3 => 'Ngoại ngữ 1',
            17 => 'Khoa học tự nhiên',
            6 => 'Lịch sử và Địa lí',
            14 => 'Tin học',
            15 => 'Công nghệ',
        ];

        $commentNames = [
            9 => 'Giáo dục thể chất',
            26 => 'Âm nhạc',
            27 => 'Mĩ thuật',
            16 => 'Hoạt động trải nghiệm, hướng nghiệp',
        ];

        $isListScores = array_is_list($scoreGrades);
        $subjects = [];
        $i = 0;
        foreach ($scoreNames as $id => $name) {
            $grade = $isListScores ? ($scoreGrades[$i] ?? 8.0) : ($scoreGrades[$id] ?? 8.0);
            $subjects[] = new SubjectInput(
                subjectId: $id,
                name: $name,
                method: 'score',
                isRequired: true,
                hk1Grade: $grade,
                hk2Grade: $grade,
            );
            $i++;
        }

        $isListComments = array_is_list($commentLevels);
        $j = 0;
        foreach ($commentNames as $id => $name) {
            $level = $isListComments ? ($commentLevels[$j] ?? 'achieved') : ($commentLevels[$id] ?? 'achieved');
            $subjects[] = new SubjectInput(
                subjectId: $id,
                name: $name,
                method: 'comment',
                isRequired: true,
                hk1Level: $level,
                hk2Level: $level,
            );
            $j++;
        }

        return $subjects;
    }

    public function test_all_good_grades_yields_good_rating(): void
    {
        // 7 subjects: all 8.5
        $subjects = $this->createStandardSecondarySubjects([8.5, 8.5, 8.5, 8.5, 8.5, 8.5, 8.5]);
        $input = new ReportCardInput('lower_secondary', 6, 'semester_1', $subjects);

        $result = $this->calculator->calculate($input);

        $this->assertSame('ok', $result->status);
        $this->assertSame('good', $result->rating);
        $this->assertFalse($result->adjustment['applied']);
    }

    public function test_exactly_five_subjects_ge_8_yields_fair_not_good(): void
    {
        // 5 subjects >= 8.0, 2 subjects 7.5 (all >= 6.5)
        $subjects = $this->createStandardSecondarySubjects([8.0, 8.0, 8.0, 8.0, 8.0, 7.5, 7.5]);
        $input = new ReportCardInput('lower_secondary', 6, 'semester_1', $subjects);

        $result = $this->calculator->calculate($input);

        $this->assertSame('ok', $result->status);
        $this->assertSame('fair', $result->rating);
    }

    public function test_boundary_eight_point_zero(): void
    {
        // 6 subjects 8.0, 1 subject 6.5 -> Good (Tốt)
        $subjects = $this->createStandardSecondarySubjects([8.0, 8.0, 8.0, 8.0, 8.0, 8.0, 6.5]);
        $input = new ReportCardInput('lower_secondary', 6, 'semester_1', $subjects);
        $this->assertSame('good', $this->calculator->calculate($input)->rating);

        // 6 subjects 7.9, 1 subject 6.5 -> Fair (Khá)
        $subjects2 = $this->createStandardSecondarySubjects([7.9, 7.9, 7.9, 7.9, 7.9, 7.9, 6.5]);
        $input2 = new ReportCardInput('lower_secondary', 6, 'semester_1', $subjects2);
        $this->assertSame('fair', $this->calculator->calculate($input2)->rating);
    }

    public function test_comment_subjects_not_achieved_impact(): void
    {
        // 1 comment not_achieved, all scores >= 8.0 -> Achieved (Đạt)
        $subjects = $this->createStandardSecondarySubjects(
            [8.5, 8.5, 8.5, 8.5, 8.5, 8.5, 8.5],
            [9 => 'not_achieved', 26 => 'achieved', 27 => 'achieved', 16 => 'achieved']
        );
        $input = new ReportCardInput('lower_secondary', 6, 'semester_1', $subjects);
        $result = $this->calculator->calculate($input);

        // Note: 1 comment subject pulled it from Good down to Achieved (diff: 3 - 1 = 2)
        // With adjustment rule, it should adjust up by 1 rank to Fair!
        $this->assertSame('ok', $result->status);
        $this->assertTrue($result->adjustment['applied']);
        $this->assertSame('fair', $result->rating);

        // 2 comments not_achieved -> Not achieved (Chưa đạt), no adjustment
        $subjects2 = $this->createStandardSecondarySubjects(
            [8.5, 8.5, 8.5, 8.5, 8.5, 8.5, 8.5],
            [9 => 'not_achieved', 26 => 'not_achieved', 27 => 'achieved', 16 => 'achieved']
        );
        $input2 = new ReportCardInput('lower_secondary', 6, 'semester_1', $subjects2);
        $result2 = $this->calculator->calculate($input2);
        $this->assertSame('not_achieved', $result2->rating);
        $this->assertFalse($result2->adjustment['applied']);
    }

    public function test_tt22_adjustment_rule_for_single_score_subject(): void
    {
        // 6 subjects 8.5, but subject 15 (Công nghệ) has 3.5
        // Base rank: Achieved (Đạt) because min score is 3.5 (< 5.0)
        // If subject 15 was 10.0, rank would be Good (Tốt)
        // Jump is from 1 to 3 (diff = 2 >= 2) -> adjusted to Fair (Khá)!
        $subjects = $this->createStandardSecondarySubjects([
            2 => 8.5,
            13 => 8.5,
            3 => 8.5,
            17 => 8.5,
            6 => 8.5,
            14 => 8.5,
            15 => 3.5,
        ]);
        $input = new ReportCardInput('lower_secondary', 6, 'semester_1', $subjects);

        $result = $this->calculator->calculate($input);

        $this->assertSame('ok', $result->status);
        $this->assertSame('fair', $result->rating);
        $this->assertTrue($result->adjustment['applied']);
        $this->assertSame(15, $result->adjustment['by_subject_id']);
    }

    public function test_tt22_adjustment_not_applied_when_two_subjects_are_low(): void
    {
        // 2 subjects are 3.5 -> neither alone can jump rank by >= 2
        $subjects = $this->createStandardSecondarySubjects([
            2 => 8.5,
            13 => 8.5,
            3 => 8.5,
            17 => 8.5,
            6 => 8.5,
            14 => 3.5,
            15 => 3.5,
        ]);
        $input = new ReportCardInput('lower_secondary', 6, 'semester_1', $subjects);

        $result = $this->calculator->calculate($input);

        $this->assertSame('not_achieved', $result->rating);
        $this->assertFalse($result->adjustment['applied']);
    }

    public function test_missing_required_subject_returns_incomplete(): void
    {
        $subjects = $this->createStandardSecondarySubjects();
        // Remove grade from Math (id 2)
        $subjects[0]->hk1Grade = null;
        $subjects[0]->hk2Grade = null;

        $input = new ReportCardInput('lower_secondary', 6, 'semester_1', $subjects);
        $result = $this->calculator->calculate($input);

        $this->assertSame('incomplete', $result->status);
        $this->assertNotEmpty($result->missing);
        $this->assertSame(2, $result->missing[0]['subject_id']);
    }

    public function test_fewer_than_six_score_subjects_returns_incomplete(): void
    {
        // Only 5 score subjects provided
        $subjects = [
            new SubjectInput(2, 'Toán', 'score', true, 1, 8.0, 8.0),
            new SubjectInput(13, 'Ngữ văn', 'score', true, 2, 8.0, 8.0),
            new SubjectInput(3, 'Ngoại ngữ 1', 'score', true, 3, 8.0, 8.0),
            new SubjectInput(17, 'KHTN', 'score', true, 4, 8.0, 8.0),
            new SubjectInput(6, 'Lịch sử và Địa lí', 'score', true, 5, 8.0, 8.0),
            new SubjectInput(9, 'GDTC', 'comment', true, 6, null, null, null, 'achieved', 'achieved'),
        ];
        $input = new ReportCardInput('lower_secondary', 6, 'semester_1', $subjects);

        $result = $this->calculator->calculate($input);

        $this->assertSame('incomplete', $result->status);
    }

    public function test_full_year_computes_formula_and_source(): void
    {
        // Math: hk1 = 8.0, hk2 = 7.0 -> full year = (8.0 + 2*7.0)/3 = 7.3
        $subjects = $this->createStandardSecondarySubjects([
            2 => 8.0,
            13 => 8.0,
            3 => 8.0,
            17 => 8.0,
            6 => 8.0,
            14 => 8.0,
            15 => 8.0,
        ]);
        // Set HK2 to 7.0 for Math
        $subjects[0]->hk2Grade = 7.0;

        $input = new ReportCardInput('lower_secondary', 6, 'full_year', $subjects);
        $result = $this->calculator->calculate($input);

        $this->assertSame('ok', $result->status);
        $mathResult = collect($result->subjects)->firstWhere('subjectId', 2);
        $this->assertNotNull($mathResult);
        $this->assertSame(7.3, $mathResult->value);
        $this->assertSame('computed', $mathResult->source);
        $this->assertSame('(8 + 2×7)/3', $mathResult->formula);
    }

    public function test_full_year_manual_fallback_when_hk1_missing(): void
    {
        $subjects = $this->createStandardSecondarySubjects();
        // Math: missing hk1, but fullYearGradeManual = 7.5
        $subjects[0]->hk1Grade = null;
        $subjects[0]->hk2Grade = 7.0;
        $subjects[0]->fullYearGradeManual = 7.5;
        $subjects[0]->fullYearSourceManual = 'manual';

        $input = new ReportCardInput('lower_secondary', 6, 'full_year', $subjects);
        $result = $this->calculator->calculate($input);

        $this->assertSame('ok', $result->status);
        $mathResult = collect($result->subjects)->firstWhere('subjectId', 2);
        $this->assertSame(7.5, $mathResult->value);
        $this->assertSame('manual', $mathResult->source);
    }
}
