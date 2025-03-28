<?php

namespace App\Api\V1\Http\Resources\ChildEvaluation\ChildEvaluation;

use App\Api\V1\Http\Resources\ChildEvaluation\CapabilityGrade\CapabilityGradeShowResource;
use App\Api\V1\Http\Resources\ChildEvaluation\ChildEvaluationSemesterResource;
use App\Api\V1\Http\Resources\ChildEvaluation\QualitityGrade\QualityGradeShowResource;
use App\Api\V1\Http\Resources\ChildEvaluation\SubjectGradeResource;
use App\Api\V1\Support\CheckPackage;
use App\Models\SubjectGrade;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ChildEvaluationSearchResource extends JsonResource
{
    use CheckPackage;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     * @throws Exception
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->id,
            'class_grade' => [
                'class_grade_id' => $this->class_grade_id,
            ],
            'average_score' => $this->average_score,
            'semester' => $this->semester,
            'status' => $this->status,
            'conduct' => $this->conduct,
            'academic_performance' => $this->academic_performance,
            'created_at' => format_datetime($this->created_at),
            'updated_at' => format_datetime($this->updated_at),
            'subjects' => SubjectGradeResource::collection($this->subjectGrades),
            'qualities' =>QualityGradeShowResource::collection($this->qualities),
            'capabilities' => CapabilityGradeShowResource::collection($this->capabilities)
        ];
    }


}
