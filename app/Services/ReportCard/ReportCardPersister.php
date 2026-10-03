<?php

namespace App\Services\ReportCard;

use App\Enums\ActiveStatus;
use App\Enums\Semester\SemesterStatus;
use App\Models\ChildEvaluation;
use App\Models\ClassGrade;
use App\Models\SubjectGrade;
use App\Services\ReportCard\Dto\CalculationResult;
use App\Services\ReportCard\Dto\ReportCardInput;
use App\Services\ReportCard\Support\Score;

class ReportCardPersister
{
    public function persist(
        ClassGrade $classGrade,
        string $semester,
        CalculationResult $result,
        ReportCardInput $input
    ): ?ChildEvaluation {
        $evaluation = $classGrade->evaluations()
            ->where('semester', $semester)
            ->first();

        // Xử lý tự tạo bản ghi cả năm nếu chưa có và đủ điều kiện
        if (!$evaluation && $semester === SemesterStatus::FullYear->value) {
            if (!$this->shouldAutoCreateFullYear($classGrade, $input)) {
                return null;
            }

            $evaluation = new ChildEvaluation([
                'class_grade_id' => $classGrade->id,
                'semester' => SemesterStatus::FullYear,
                'status' => ActiveStatus::Active,
            ]);
        }

        if (!$evaluation) {
            return null;
        }

        // 1. Luôn ghi kết quả tính toán hệ thống
        $evaluation->calculated_academic_performance = $result->isOk() ? $result->rating : null;
        $evaluation->calculation_status = $result->status;
        $evaluation->calculation_snapshot = $result->snapshot();
        $evaluation->calculation_version = $result->version;
        $evaluation->calculated_at = now();

        // 2. Tự động cập nhật academic_performance nếu bật cờ và không bị override
        if (config('report_card.auto_classification', false)) {
            if ($result->isOk() && !$evaluation->is_performance_overridden && $result->rating !== null) {
                $evaluation->academic_performance = $result->rating;
            }
        }

        $evaluation->save();

        // 3. Cập nhật subject_grades cả năm nếu có kết quả computed
        if ($semester === SemesterStatus::FullYear->value) {
            $this->persistFullYearSubjectGrades($evaluation, $result);
        }

        // 4. Đồng bộ điểm trung bình vào class_grades
        $this->syncClassGradeAverages($classGrade, $semester, $result, $input);

        return $evaluation;
    }

    private function shouldAutoCreateFullYear(ClassGrade $classGrade, ReportCardInput $input): bool
    {
        if (!config('report_card.auto_full_year', false)) {
            return false;
        }

        $sem1 = $classGrade->evaluations()->where('semester', SemesterStatus::Semester1)->exists();
        $sem2 = $classGrade->evaluations()->where('semester', SemesterStatus::Semester2)->exists();

        if ($input->educationLevel === 'primary') {
            return $sem2;
        }

        return $sem1 && $sem2;
    }

    private function persistFullYearSubjectGrades(ChildEvaluation $evaluation, CalculationResult $result): void
    {
        $autoFullYear = config('report_card.auto_full_year', false);

        foreach ($result->subjects as $sr) {
            if ($sr->value === null) {
                continue;
            }

            $sg = SubjectGrade::firstOrNew([
                'child_evaluation_id' => $evaluation->id,
                'subject_id' => $sr->subjectId,
            ]);

            if ($sr->method === 'score') {
                if ($autoFullYear && $sr->source === 'computed' && $sg->full_year_grade_source?->value !== 'overridden') {
                    $sg->full_year_grade = (float) $sr->value;
                    $sg->full_year_grade_source = 'computed';
                }
            } elseif ($sr->method === 'comment' || $sr->method === 'level') {
                if ($autoFullYear && empty($sg->achievement_level)) {
                    $sg->achievement_level = (string) $sr->value;
                }
            }

            $sg->save();
        }
    }

    private function syncClassGradeAverages(
        ClassGrade $classGrade,
        string $semester,
        CalculationResult $result,
        ReportCardInput $input
    ): void {
        $scoreValues = [];
        foreach ($result->subjects as $sr) {
            if ($sr->method === 'score' && is_numeric($sr->value)) {
                $scoreValues[] = (float) $sr->value;
            }
        }

        $avg = Score::average($scoreValues, 2);

        if ($semester === SemesterStatus::Semester1->value) {
            if ($avg !== null) {
                $classGrade->semester1_grade = $avg;
            }
        } elseif ($semester === SemesterStatus::Semester2->value) {
            if ($avg !== null) {
                $classGrade->semester2_grade = $avg;
            }
        } elseif ($semester === SemesterStatus::FullYear->value) {
            if ($input->educationLevel === 'primary') {
                if ($classGrade->semester2_grade !== null) {
                    $classGrade->full_year_grade = $classGrade->semester2_grade;
                } elseif ($avg !== null) {
                    $classGrade->full_year_grade = $avg;
                }
            } else {
                if ($classGrade->semester1_grade !== null && $classGrade->semester2_grade !== null) {
                    $classGrade->full_year_grade = round(
                        ($classGrade->semester1_grade + 2 * $classGrade->semester2_grade) / 3,
                        2
                    );
                } elseif ($avg !== null) {
                    $classGrade->full_year_grade = $avg;
                }
            }
        }

        $classGrade->save();
    }
}
