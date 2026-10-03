<?php

namespace App\Services\ReportCard\Dto;

namespace App\Services\ReportCard;

use App\Services\ReportCard\Contracts\ReportCardCalculator;
use App\Services\ReportCard\Dto\CalculationResult;
use App\Services\ReportCard\Dto\ReportCardInput;
use App\Services\ReportCard\Dto\SubjectInput;
use App\Services\ReportCard\Dto\SubjectResult;
use App\Services\ReportCard\Support\RatingLadder;
use App\Services\ReportCard\Support\Score;

class SecondaryCalculator implements ReportCardCalculator
{
    public const REGULATION = 'TT22/2021';
    public const VERSION = 1;

    public function calculate(ReportCardInput $input): CalculationResult
    {
        $subjectResults = [];
        $missing = [];
        $warnings = [];

        $evaluatedScores = [];      // subjectId => float
        $evaluatedComments = [];    // subjectId => 'achieved'|'not_achieved'
        $subjectTypes = [];         // subjectId => 'score'|'comment'

        $semester = $input->semester;

        foreach ($input->subjects as $subject) {
            $res = $this->evaluateSubject($subject, $semester, $warnings);
            $subjectResults[] = $res;

            if ($res->value === null) {
                if ($subject->isRequired) {
                    $missing[] = [
                        'subject_id' => $subject->subjectId,
                        'name' => $subject->name,
                        'reason' => "Chưa có kết quả môn bắt buộc ({$subject->name})",
                    ];
                }
                continue;
            }

            if ($res->method === 'score') {
                if (!is_numeric($res->value) || $res->value < 0 || $res->value > 10) {
                    return CalculationResult::invalidInput(
                        warnings: ["Điểm môn {$subject->name} không hợp lệ (giá trị: {$res->value}, hợp lệ: 0–10)"],
                        subjects: $subjectResults,
                        regulation: self::REGULATION,
                        educationLevel: $input->educationLevel,
                        semester: $semester,
                        version: self::VERSION,
                    );
                }
                $evaluatedScores[$subject->subjectId] = (float) $res->value;
                $subjectTypes[$subject->subjectId] = 'score';
            } elseif ($res->method === 'comment') {
                $commentValue = $this->normalizeCommentValue((string) $res->value, $subject->name, $warnings);
                if ($commentValue === null) {
                    return CalculationResult::invalidInput(
                        warnings: ["Kết quả môn {$subject->name} không hợp lệ cho cấp THCS/THPT (giá trị: {$res->value})"],
                        subjects: $subjectResults,
                        regulation: self::REGULATION,
                        educationLevel: $input->educationLevel,
                        semester: $semester,
                        version: self::VERSION,
                    );
                }
                $evaluatedComments[$subject->subjectId] = $commentValue;
                $subjectTypes[$subject->subjectId] = 'comment';
            }
        }

        // TT22 Điều 9 yêu cầu có ít nhất 6 môn tính điểm để xếp loại
        if (count($evaluatedScores) < 6) {
            $missing[] = [
                'subject_id' => null,
                'name' => 'Số môn có điểm số',
                'reason' => 'Cần ít nhất 6 môn có điểm số để xếp loại theo TT22 (hiện có: ' . count($evaluatedScores) . ')',
            ];
        }

        if (!empty($missing)) {
            return CalculationResult::incomplete(
                missing: $missing,
                subjects: $subjectResults,
                warnings: $warnings,
                regulation: self::REGULATION,
                educationLevel: $input->educationLevel,
                semester: $semester,
                version: self::VERSION,
            );
        }

        // Xếp loại cơ sở (base rank)
        $ruleStats = [];
        $baseRank = $this->classifyRank($evaluatedScores, $evaluatedComments, $ruleStats);

        // Quy tắc điều chỉnh (TT22 Điều 9 Khoản 3)
        $adjustment = ['applied' => false, 'by_subject_id' => null, 'reason' => null];
        $finalRank = $baseRank;

        if ($baseRank < RatingLadder::RANK_GOOD) {
            foreach ($subjectTypes as $subjectId => $type) {
                if ($type === 'score') {
                    $fixedScores = $evaluatedScores;
                    $fixedScores[$subjectId] = 10.0;
                    $hypotheticalRank = $this->classifyRank($fixedScores, $evaluatedComments);
                } else {
                    $fixedComments = $evaluatedComments;
                    $fixedComments[$subjectId] = 'achieved';
                    $hypotheticalRank = $this->classifyRank($evaluatedScores, $fixedComments);
                }

                if ($hypotheticalRank - $baseRank >= 2) {
                    $finalRank = $baseRank + 1;
                    $adjustment = [
                        'applied' => true,
                        'by_subject_id' => $subjectId,
                        'reason' => sprintf(
                            'Nâng 1 mức từ %s lên %s do chỉ có 01 môn (id: %d) làm hạ mức đánh giá',
                            RatingLadder::getLabel(RatingLadder::rankToRating($baseRank)),
                            RatingLadder::getLabel(RatingLadder::rankToRating($finalRank)),
                            $subjectId
                        ),
                    ];
                    break;
                }
            }
        }

        $finalRating = RatingLadder::rankToRating($finalRank);

        // Đánh dấu passed cho từng môn
        foreach ($subjectResults as $sr) {
            if ($sr->method === 'score' && $sr->value !== null) {
                $sr->passed = ((float) $sr->value) >= 5.0;
            } elseif ($sr->method === 'comment' && $sr->value !== null) {
                $sr->passed = in_array($sr->value, ['achieved', 'excellent', 'completed'], true);
            }
        }

        return new CalculationResult(
            status: 'ok',
            rating: $finalRating,
            subjects: $subjectResults,
            missing: [],
            warnings: $warnings,
            adjustment: $adjustment,
            rules: array_merge($ruleStats, ['matched' => RatingLadder::rankToRating($baseRank)]),
            regulation: self::REGULATION,
            educationLevel: $input->educationLevel,
            semester: $semester,
            version: self::VERSION,
        );
    }

    private function evaluateSubject(SubjectInput $subject, string $semester, array &$warnings): SubjectResult
    {
        if ($subject->method === 'score') {
            return $this->evaluateScoreSubject($subject, $semester, $warnings);
        }

        return $this->evaluateCommentSubject($subject, $semester, $warnings);
    }

    private function evaluateScoreSubject(SubjectInput $subject, string $semester, array &$warnings): SubjectResult
    {
        $hk1 = $subject->hk1Grade;
        $hk2 = $subject->hk2Grade;

        if ($semester === 'semester_1') {
            return new SubjectResult(
                subjectId: $subject->subjectId,
                name: $subject->name,
                method: 'score',
                isRequired: $subject->isRequired,
                value: $hk1,
                source: $hk1 !== null ? 'hk1' : null,
                hk1: $hk1,
            );
        }

        if ($semester === 'semester_2') {
            return new SubjectResult(
                subjectId: $subject->subjectId,
                name: $subject->name,
                method: 'score',
                isRequired: $subject->isRequired,
                value: $hk2,
                source: $hk2 !== null ? 'hk2' : null,
                hk2: $hk2,
            );
        }

        // Cả năm (full_year)
        if ($hk1 !== null && $hk2 !== null) {
            $hk1H = Score::toHundredths($hk1);
            $hk2H = Score::toHundredths($hk2);
            $fyTenths = Score::fullYear($hk1H, $hk2H);
            $computedValue = Score::fromTenths($fyTenths);

            return new SubjectResult(
                subjectId: $subject->subjectId,
                name: $subject->name,
                method: 'score',
                isRequired: $subject->isRequired,
                value: $computedValue,
                source: 'computed',
                formula: sprintf('(%s + 2×%s)/3', $hk1, $hk2),
                hk1: $hk1,
                hk2: $hk2,
            );
        }

        if ($subject->fullYearGradeManual !== null) {
            $manualVal = round((float) $subject->fullYearGradeManual, 1);
            $src = $subject->fullYearSourceManual ?? 'manual';
            return new SubjectResult(
                subjectId: $subject->subjectId,
                name: $subject->name,
                method: 'score',
                isRequired: $subject->isRequired,
                value: $manualVal,
                source: $src,
                formula: null,
                hk1: $hk1,
                hk2: $hk2,
                notes: ['Dùng điểm cả năm do thiếu điểm HK1 hoặc HK2'],
            );
        }

        return new SubjectResult(
            subjectId: $subject->subjectId,
            name: $subject->name,
            method: 'score',
            isRequired: $subject->isRequired,
            value: null,
            source: null,
            hk1: $hk1,
            hk2: $hk2,
        );
    }

    private function evaluateCommentSubject(SubjectInput $subject, string $semester, array &$warnings): SubjectResult
    {
        $hk1 = $subject->hk1Level;
        $hk2 = $subject->hk2Level;

        if ($semester === 'semester_1') {
            return new SubjectResult(
                subjectId: $subject->subjectId,
                name: $subject->name,
                method: 'comment',
                isRequired: $subject->isRequired,
                value: $hk1,
                source: $hk1 !== null ? 'hk1' : null,
                hk1: $hk1,
            );
        }

        if ($semester === 'semester_2') {
            return new SubjectResult(
                subjectId: $subject->subjectId,
                name: $subject->name,
                method: 'comment',
                isRequired: $subject->isRequired,
                value: $hk2,
                source: $hk2 !== null ? 'hk2' : null,
                hk2: $hk2,
            );
        }

        // Cả năm (full_year): TT22 quy định kết quả cả năm của môn nhận xét là kết quả HK2
        if ($hk2 !== null) {
            return new SubjectResult(
                subjectId: $subject->subjectId,
                name: $subject->name,
                method: 'comment',
                isRequired: $subject->isRequired,
                value: $hk2,
                source: 'hk2',
                hk1: $hk1,
                hk2: $hk2,
            );
        }

        if ($subject->fullYearLevelManual !== null) {
            return new SubjectResult(
                subjectId: $subject->subjectId,
                name: $subject->name,
                method: 'comment',
                isRequired: $subject->isRequired,
                value: $subject->fullYearLevelManual,
                source: 'manual',
                hk1: $hk1,
                hk2: $hk2,
            );
        }

        return new SubjectResult(
            subjectId: $subject->subjectId,
            name: $subject->name,
            method: 'comment',
            isRequired: $subject->isRequired,
            value: null,
            source: null,
            hk1: $hk1,
            hk2: $hk2,
        );
    }

    private function normalizeCommentValue(string $val, string $subjectName, array &$warnings): ?string
    {
        $normalized = strtolower(trim($val));
        if (in_array($normalized, ['achieved', 'not_achieved'], true)) {
            return $normalized;
        }

        // Graceful mapping for legacy data in secondary
        if (in_array($normalized, ['excellent', 'completed'], true)) {
            $warnings[] = "Môn {$subjectName} có mức '{$val}' được quy về 'achieved' (Đạt) theo TT22";
            return 'achieved';
        }

        if ($normalized === 'not_completed') {
            $warnings[] = "Môn {$subjectName} có mức '{$val}' được quy về 'not_achieved' (Chưa đạt) theo TT22";
            return 'not_achieved';
        }

        return null;
    }

    private function classifyRank(array $scores, array $comments, array &$stats = []): int
    {
        $countGe80 = 0;
        $countGe65 = 0;
        $countGe50 = 0;
        $minScore = 10.0;

        foreach ($scores as $s) {
            if ($s >= 7.999) {
                $countGe80++;
            }
            if ($s >= 6.499) {
                $countGe65++;
            }
            if ($s >= 4.999) {
                $countGe50++;
            }
            if ($s < $minScore) {
                $minScore = $s;
            }
        }

        $commentNotAchieved = 0;
        foreach ($comments as $c) {
            if ($c === 'not_achieved') {
                $commentNotAchieved++;
            }
        }

        $stats = [
            'count_ge_8_0' => $countGe80,
            'count_ge_6_5' => $countGe65,
            'count_ge_5_0' => $countGe50,
            'min_score' => round($minScore, 1),
            'comment_not_achieved' => $commentNotAchieved,
        ];

        // Tốt (Good / 3): Tất cả nhận xét Đạt + tất cả điểm >= 6.5 + >= 6 môn >= 8.0
        if ($commentNotAchieved === 0 && $minScore >= 6.499 && $countGe80 >= 6) {
            return RatingLadder::RANK_GOOD;
        }

        // Khá (Fair / 2): Tất cả nhận xét Đạt + tất cả điểm >= 5.0 + >= 6 môn >= 6.5
        if ($commentNotAchieved === 0 && $minScore >= 4.999 && $countGe65 >= 6) {
            return RatingLadder::RANK_FAIR;
        }

        // Đạt (Achieved / 1): Tối đa 1 môn Chưa đạt + không môn nào < 3.5 + >= 6 môn >= 5.0
        if ($commentNotAchieved <= 1 && $minScore >= 3.499 && $countGe50 >= 6) {
            return RatingLadder::RANK_ACHIEVED;
        }

        // Chưa đạt (NotAchieved / 0)
        return RatingLadder::RANK_NOT_ACHIEVED;
    }
}
