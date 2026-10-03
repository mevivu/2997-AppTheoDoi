<?php

namespace App\Services\ReportCard;

use App\Services\ReportCard\Contracts\ReportCardCalculator;
use App\Services\ReportCard\Dto\CalculationResult;
use App\Services\ReportCard\Dto\ReportCardInput;
use App\Services\ReportCard\Dto\SubjectInput;
use App\Services\ReportCard\Dto\SubjectResult;

class PrimaryCalculator implements ReportCardCalculator
{
    public const REGULATION = 'TT27/2020';
    public const VERSION = 1;

    public function calculate(ReportCardInput $input): CalculationResult
    {
        $subjectResults = [];
        $missing = [];
        $warnings = [];

        $semester = $input->semester;
        $scope = ($semester === 'semester_1') ? 'reference' : 'official';

        if ($semester === 'semester_1') {
            $warnings[] = 'Kết quả HK1 cấp Tiểu học mang tính chất tham khảo (TT27 chỉ khen thưởng và đánh giá chính thức cuối năm)';
        }

        $allSubjectLevels = [];  // subjectId => 'excellent'|'completed'|'not_completed'
        $allTestScores = [];     // subjectId => float

        foreach ($input->subjects as $subject) {
            $res = $this->evaluateSubject($subject, $semester, $warnings);
            $subjectResults[] = $res;

            $level = $res->value;
            $testScore = $res->hk2 !== null ? $res->hk2 : $res->hk1; // test score stored in hk1/hk2

            // Kiểm tra lỗi F5 / Q4: Dùng achieved/not_achieved ở tiểu học
            if ($level !== null && in_array(strtolower((string) $level), ['achieved', 'not_achieved'], true)) {
                return CalculationResult::invalidInput(
                    warnings: [
                        "Môn '{$subject->name}' có mức đánh giá '{$level}' không phù hợp với cấp Tiểu học theo TT27 (yêu cầu Hoàn thành tốt / Hoàn thành / Chưa hoàn thành). Vui lòng cập nhật lại."
                    ],
                    subjects: $subjectResults,
                    regulation: self::REGULATION,
                    educationLevel: $input->educationLevel,
                    semester: $semester,
                    version: self::VERSION,
                );
            }

            if ($level === null) {
                if ($subject->isRequired) {
                    $missing[] = [
                        'subject_id' => $subject->subjectId,
                        'name' => $subject->name,
                        'reason' => "Chưa có mức đánh giá môn bắt buộc ({$subject->name})",
                    ];
                }
            } else {
                $normalizedLevel = strtolower((string) $level);
                if (!in_array($normalizedLevel, ['excellent', 'completed', 'not_completed'], true)) {
                    return CalculationResult::invalidInput(
                        warnings: ["Mức đánh giá '{$level}' của môn {$subject->name} không hợp lệ theo TT27"],
                        subjects: $subjectResults,
                        regulation: self::REGULATION,
                        educationLevel: $input->educationLevel,
                        semester: $semester,
                        version: self::VERSION,
                    );
                }
                $allSubjectLevels[$subject->subjectId] = $normalizedLevel;
            }

            // Với môn level_with_score, bắt buộc phải có điểm KTĐK
            if ($subject->method === 'level_with_score') {
                if ($testScore === null) {
                    if ($subject->isRequired) {
                        $missing[] = [
                            'subject_id' => $subject->subjectId,
                            'name' => $subject->name,
                            'reason' => "Chưa có điểm kiểm tra định kỳ môn {$subject->name}",
                        ];
                    }
                } else {
                    $numericScore = (float) $testScore;
                    if ($numericScore < 0 || $numericScore > 10) {
                        return CalculationResult::invalidInput(
                            warnings: ["Điểm KTĐK môn {$subject->name} không hợp lệ (giá trị: {$testScore}, hợp lệ: 0–10)"],
                            subjects: $subjectResults,
                            regulation: self::REGULATION,
                            educationLevel: $input->educationLevel,
                            semester: $semester,
                            version: self::VERSION,
                        );
                    }
                    $allTestScores[$subject->subjectId] = $numericScore;
                }
            }
        }

        // Kiểm tra phẩm chất và năng lực
        $qualityStatuses = [];
        foreach ($input->qualities as $q) {
            $status = $q['status'] ?? null;
            if ($status === null || !in_array($status, ['good', 'achieved', 'not_achieved'], true)) {
                $missing[] = [
                    'subject_id' => null,
                    'name' => 'Phẩm chất: ' . ($q['name'] ?? 'Chưa rõ'),
                    'reason' => 'Chưa có đánh giá phẩm chất (Tốt / Đạt / Cần cố gắng)',
                ];
            } else {
                $qualityStatuses[] = $status;
            }
        }

        $capabilityStatuses = [];
        foreach ($input->capabilities as $c) {
            $status = $c['status'] ?? null;
            if ($status === null || !in_array($status, ['good', 'achieved', 'not_achieved'], true)) {
                $missing[] = [
                    'subject_id' => null,
                    'name' => 'Năng lực: ' . ($c['name'] ?? 'Chưa rõ'),
                    'reason' => 'Chưa có đánh giá năng lực (Tốt / Đạt / Cần cố gắng)',
                ];
            } else {
                $capabilityStatuses[] = $status;
            }
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

        // Xếp loại TT27
        $rulesStats = [];
        $rating = $this->classifyPrimary(
            $allSubjectLevels,
            $allTestScores,
            $qualityStatuses,
            $capabilityStatuses,
            $rulesStats
        );

        // Đánh dấu passed cho từng môn
        foreach ($subjectResults as $sr) {
            if ($sr->value !== null) {
                $sr->passed = in_array($sr->value, ['excellent', 'completed'], true);
            }
        }

        return new CalculationResult(
            status: 'ok',
            rating: $rating,
            subjects: $subjectResults,
            missing: [],
            warnings: $warnings,
            adjustment: ['applied' => false, 'by_subject_id' => null, 'reason' => null],
            rules: $rulesStats,
            regulation: self::REGULATION,
            educationLevel: $input->educationLevel,
            semester: $semester,
            version: self::VERSION,
            scope: $scope,
        );
    }

    private function evaluateSubject(SubjectInput $subject, string $semester, array &$warnings): SubjectResult
    {
        // Với tiểu học, level có thể lấy từ achievementLevel (nếu truyền trực tiếp) hoặc hk1Level / hk2Level
        $level = null;
        $testScore = null;
        $source = null;

        if ($semester === 'semester_1') {
            $level = $subject->hk1Level ?? $subject->achievementLevel;
            $testScore = $subject->hk1Grade ?? $subject->grade;
            $source = 'hk1';
        } elseif ($semester === 'semester_2') {
            $level = $subject->hk2Level ?? $subject->achievementLevel;
            $testScore = $subject->hk2Grade ?? $subject->grade;
            $source = 'hk2';
        } else {
            // full_year: Theo TT27 lấy kết quả HK2
            if ($subject->hk2Level !== null) {
                $level = $subject->hk2Level;
                $testScore = $subject->hk2Grade;
                $source = 'hk2';
            } elseif ($subject->achievementLevel !== null) {
                $level = $subject->achievementLevel;
                $testScore = $subject->grade;
                $source = 'manual';
                $warnings[] = "Môn {$subject->name} sử dụng dữ liệu nhập trực tiếp cho cả năm";
            }
        }

        return new SubjectResult(
            subjectId: $subject->subjectId,
            name: $subject->name,
            method: $subject->method,
            isRequired: $subject->isRequired,
            value: $level,
            source: $level !== null ? $source : null,
            formula: null,
            hk1: $subject->hk1Grade ?? ($subject->method === 'level_with_score' ? $testScore : null),
            hk2: $testScore,
        );
    }

    private function classifyPrimary(
        array $subjectLevels,
        array $testScores,
        array $qualityStatuses,
        array $capabilityStatuses,
        array &$stats = []
    ): string {
        $totalSubjects = count($subjectLevels);
        $excellentSubjects = count(array_filter($subjectLevels, fn($l) => $l === 'excellent'));
        $completedSubjects = count(array_filter($subjectLevels, fn($l) => $l === 'completed'));
        $notCompletedSubjects = count(array_filter($subjectLevels, fn($l) => $l === 'not_completed'));

        $minTestScore = empty($testScores) ? 10.0 : min($testScores);

        $allPersonalities = array_merge($qualityStatuses, $capabilityStatuses);
        $notGoodPersonalities = count(array_filter($allPersonalities, fn($s) => $s !== 'good'));
        $notAchievedPersonalities = count(array_filter($allPersonalities, fn($s) => $s === 'not_achieved'));

        $stats = [
            'total_subjects' => $totalSubjects,
            'excellent_subjects' => $excellentSubjects,
            'completed_subjects' => $completedSubjects,
            'not_completed_subjects' => $notCompletedSubjects,
            'min_test_score' => round($minTestScore, 1),
            'not_good_personalities' => $notGoodPersonalities,
            'not_achieved_personalities' => $notAchievedPersonalities,
        ];

        // 1. Hoàn thành xuất sắc: Tất cả môn T + Tất cả PC/NL Tốt + Tất cả điểm KTĐK >= 9
        if ($notCompletedSubjects === 0 && $completedSubjects === 0 && $notGoodPersonalities === 0 && $minTestScore >= 8.999) {
            $stats['matched'] = 'excellent';
            return 'excellent';
        }

        // 2. Hoàn thành tốt: Tất cả môn T + Tất cả PC/NL Tốt + Tất cả điểm KTĐK >= 7
        if ($notCompletedSubjects === 0 && $completedSubjects === 0 && $notGoodPersonalities === 0 && $minTestScore >= 6.999) {
            $stats['matched'] = 'completed_good';
            return 'completed_good';
        }

        // 3. Hoàn thành: Tất cả môn T hoặc H (không có C) + Tất cả PC/NL Tốt hoặc Đạt (không có Cần cố gắng) + Tất cả điểm KTĐK >= 5
        if ($notCompletedSubjects === 0 && $notAchievedPersonalities === 0 && $minTestScore >= 4.999) {
            $stats['matched'] = 'completed';
            return 'completed';
        }

        // 4. Chưa hoàn thành
        $stats['matched'] = 'not_completed';
        return 'not_completed';
    }
}
