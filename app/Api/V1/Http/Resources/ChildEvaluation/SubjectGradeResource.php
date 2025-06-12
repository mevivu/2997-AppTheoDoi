<?php

namespace App\Api\V1\Http\Resources\ChildEvaluation;

use App\Enums\Class\LevelGroup;
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
//        $evaluation = $this->childEvaluation;
//        $classGrade = $evaluation->classGrade;
//        $levelGroup = $classGrade->class?->level_group;
//
        $subjectId = $this->subject_id;
//
//        // Tải các đánh giá 2 học kỳ (eager loaded từ controller/service trước đó)
//        $allEvaluations = $classGrade->evaluations;
//
//        $semester1 = $allEvaluations->firstWhere('semester', SemesterStatus::Semester1);
//        $semester2 = $allEvaluations->firstWhere('semester', SemesterStatus::Semester2);
//
//        $semester1Grade = $semester1?->subjectGrades->firstWhere('subject_id', $subjectId)?->grade;
//        $semester2Grade = $semester2?->subjectGrades->firstWhere('subject_id', $subjectId)?->grade;
//
//        // Tính điểm cả năm theo công thức
//        $fullYearSubjectGrade = null;
//        if ($semester2Grade !== null) {
//            if ($levelGroup === LevelGroup::Junior) {
//                $fullYearSubjectGrade = $semester2Grade;
//            } else {
//                $s1 = $semester1Grade ?? 0;
//                $fullYearSubjectGrade = round(($s1 + 2 * $semester2Grade) / 3, 2);
//            }
//        }

        return [
            'id' => $this->id,
            'subject_id' => $subjectId,
            'grade' => $this->grade,
            'remark' => $this->remark,
            'achievement_level' => $this->achievement_level,
            'name' => $subject->name,
            'full_year_grade' => $this->full_year_grade,
        ];
    }



}
