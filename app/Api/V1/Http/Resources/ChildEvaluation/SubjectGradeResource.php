<?php

namespace App\Api\V1\Http\Resources\ChildEvaluation;

use App\Enums\Class\EducationLevel;
use App\Enums\Semester\SemesterStatus;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class SubjectGradeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     * @throws Exception
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $subject = $this->subject;
        $subjectId = $this->subject_id;
        $fullYearGrade = $this->full_year_grade;
        $source = $this->full_year_grade_source instanceof \BackedEnum
            ? $this->full_year_grade_source->value
            : $this->full_year_grade_source;

        // Nếu trường full_year_grade trong DB chưa có nhưng đang ở học kỳ Cả năm, tự động tính fallback
        $evaluation = $this->childEvaluation;
        $semValue = $evaluation?->semester instanceof \BackedEnum
            ? $evaluation->semester->value
            : $evaluation?->semester;

        if ($fullYearGrade === null && $semValue === SemesterStatus::FullYear->value && $evaluation) {
            $classGrade = $evaluation->classGrade;
            if ($classGrade) {
                $class = $classGrade->class;
                $educationLevel = $class?->resolvedEducationLevel() ?? EducationLevel::fromClassId($classGrade->class_id);
                $isPrimary = ($educationLevel === EducationLevel::Primary);

                $allEvaluations = $classGrade->evaluations;
                $semester1 = $allEvaluations->firstWhere('semester', SemesterStatus::Semester1);
                $semester2 = $allEvaluations->firstWhere('semester', SemesterStatus::Semester2);

                $s1 = $semester1?->subjectGrades->firstWhere('subject_id', $subjectId)?->grade;
                $s2 = $semester2?->subjectGrades->firstWhere('subject_id', $subjectId)?->grade;

                if ($isPrimary) {
                    // Lớp 1-5: Điểm tbm cả năm = điểm hk2
                    if ($s2 !== null && $s2 !== '') {
                        $fullYearGrade = (float) $s2;
                        $source = 'auto';
                    }
                } else {
                    // Lớp 6-12: Điểm tbm Cả năm = (hk1 + hk2*2)/3
                    if ($s1 !== null && $s1 !== '' && $s2 !== null && $s2 !== '') {
                        $fullYearGrade = round(((float)$s1 + 2 * (float)$s2) / 3, 1);
                        $source = 'auto';
                    }
                }
            }
        }

        return [
            'id' => $this->id,
            'subject_id' => $subjectId,
            'grade' => $this->grade,
            'remark' => $this->remark,
            'achievement_level' => $this->achievement_level,
            'name' => $subject?->name,
            'full_year_grade' => $fullYearGrade !== null ? (float) $fullYearGrade : null,
            'full_year_grade_source' => $source,
        ];
    }
}
