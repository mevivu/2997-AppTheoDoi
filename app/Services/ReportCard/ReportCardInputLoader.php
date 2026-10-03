<?php

namespace App\Services\ReportCard;

use App\Enums\Semester\SemesterStatus;
use App\Models\ClassGrade;
use App\Services\ReportCard\Dto\ReportCardInput;
use App\Services\ReportCard\Dto\SubjectInput;
use Illuminate\Support\Collection;

class ReportCardInputLoader
{
    public function load(ClassGrade $classGrade, string $semester): ReportCardInput
    {
        $class = $classGrade->class;
        $educationLevel = $class ? $class->resolvedEducationLevel()->value : 'lower_secondary';

        $classSubjects = $class
            ? $class->subjects()->withPivot('evaluation_method', 'is_required', 'sort_order')->orderByPivot('sort_order')->get()
            : collect();

        // Eager load 3 evaluations
        $evaluations = $classGrade->evaluations()
            ->with(['subjectGrades', 'qualities.quality', 'capabilities.capability'])
            ->get()
            ->keyBy(fn($e) => $e->semester instanceof \BackedEnum ? $e->semester->value : (string) $e->semester);

        $sem1Eval = $evaluations->get(SemesterStatus::Semester1->value);
        $sem2Eval = $evaluations->get(SemesterStatus::Semester2->value);
        $fyEval = $evaluations->get(SemesterStatus::FullYear->value);

        $currentEval = $evaluations->get($semester);

        $subjectsInput = [];

        foreach ($classSubjects as $sub) {
            $method = $sub->pivot->evaluation_method;
            if (empty($method)) {
                $method = ($educationLevel === 'primary') ? 'level' : 'score';
            }

            $isRequired = (bool) ($sub->pivot->is_required ?? true);
            $sortOrder = (int) ($sub->pivot->sort_order ?? 0);

            $sem1Sg = $sem1Eval?->subjectGrades?->firstWhere('subject_id', $sub->id);
            $sem2Sg = $sem2Eval?->subjectGrades?->firstWhere('subject_id', $sub->id);
            $fySg = $fyEval?->subjectGrades?->firstWhere('subject_id', $sub->id);

            $currentSg = match ($semester) {
                SemesterStatus::Semester1->value => $sem1Sg,
                SemesterStatus::Semester2->value => $sem2Sg,
                default => $fySg,
            };

            $hk1Grade = $sem1Sg?->grade !== null ? (float) $sem1Sg->grade : null;
            $hk2Grade = $sem2Sg?->grade !== null ? (float) $sem2Sg->grade : null;
            $fullYearGradeManual = $fySg?->full_year_grade !== null ? (float) $fySg->full_year_grade : null;

            $hk1Level = $this->enumOrString($sem1Sg?->achievement_level);
            $hk2Level = $this->enumOrString($sem2Sg?->achievement_level);
            $fyLevel = $this->enumOrString($fySg?->achievement_level);

            $fySource = $this->enumOrString($fySg?->full_year_grade_source);

            $currentGrade = $currentSg?->grade !== null ? (float) $currentSg->grade : null;
            $currentLevel = $this->enumOrString($currentSg?->achievement_level);

            $subjectsInput[] = new SubjectInput(
                subjectId: $sub->id,
                name: $sub->name,
                method: $method,
                isRequired: $isRequired,
                sortOrder: $sortOrder,
                hk1Grade: $hk1Grade,
                hk2Grade: $hk2Grade,
                fullYearGradeManual: $fullYearGradeManual,
                hk1Level: $hk1Level,
                hk2Level: $hk2Level,
                fullYearLevelManual: $fyLevel,
                fullYearSourceManual: $fySource,
                grade: $currentGrade,
                achievementLevel: $currentLevel,
                remark: $currentSg?->remark,
            );
        }

        // Tải qualities và capabilities
        $evalForPersonalities = $currentEval;
        if ($semester === SemesterStatus::FullYear->value && (!$evalForPersonalities || $evalForPersonalities->qualities->isEmpty())) {
            $evalForPersonalities = $sem2Eval ?? $sem1Eval;
        }

        $qualities = [];
        if ($evalForPersonalities) {
            foreach ($evalForPersonalities->qualities as $cq) {
                $status = $this->enumOrString($cq->status);
                $qualities[] = [
                    'id' => $cq->quality_id,
                    'name' => $cq->quality?->name ?? 'Phẩm chất ' . $cq->quality_id,
                    'status' => $status,
                ];
            }
        }

        $capabilities = [];
        if ($evalForPersonalities) {
            foreach ($evalForPersonalities->capabilities as $cc) {
                $status = $this->enumOrString($cc->status);
                $capabilities[] = [
                    'id' => $cc->capability_id,
                    'name' => $cc->capability?->name ?? 'Năng lực ' . $cc->capability_id,
                    'status' => $status,
                ];
            }
        }

        $currentAcademicPerformance = $this->enumOrString($currentEval?->academic_performance);
        $isOverridden = (bool) ($currentEval?->is_performance_overridden ?? false);

        return new ReportCardInput(
            educationLevel: $educationLevel,
            classId: $classGrade->class_id,
            semester: $semester,
            subjects: $subjectsInput,
            qualities: $qualities,
            capabilities: $capabilities,
            currentAcademicPerformance: $currentAcademicPerformance,
            isPerformanceOverridden: $isOverridden,
        );
    }

    private function enumOrString(mixed $val): ?string
    {
        if ($val === null) {
            return null;
        }

        if ($val instanceof \BackedEnum) {
            return (string) $val->value;
        }

        return (string) $val;
    }
}
