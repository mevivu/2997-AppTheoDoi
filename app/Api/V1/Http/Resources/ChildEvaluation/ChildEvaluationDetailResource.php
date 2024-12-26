<?php

namespace App\Api\V1\Http\Resources\ChildEvaluation;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ChildEvaluationDetailResource extends JsonResource
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
        return [
            'id' => $this->id,
            'class' => [
                'id' => $this->classGrade->class->id,
                'name' => $this->classGrade->class->name
            ],
            'semester' => $this->semester,
            'average_score' => $this->average_score,
            'conduct' => $this->conduct,
            'academic_performance' => $this->academic_performance,
            'status' => $this->status,
            'subjects' => SubjectGradeResource::collection($this->subjectGrades),
            'capabilities' => ChildCapabilityResource::collection($this->capabilities),
            'qualities' => ChildQualityResource::collection($this->qualities)

        ];
    }


}
