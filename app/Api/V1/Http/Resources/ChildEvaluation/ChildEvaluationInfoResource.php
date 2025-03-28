<?php

namespace App\Api\V1\Http\Resources\ChildEvaluation;

use App\Api\V1\Http\Resources\Capability\CapabilityResource;
use App\Api\V1\Http\Resources\ChildEvaluation\ChildEvaluation\ChildEvaluationSearchResource;
use App\Api\V1\Http\Resources\Classes\ClassesResource;
use App\Api\V1\Http\Resources\Subject\SubjectResource;
use App\Enums\ChildEvaluation\AcademicRating;
use App\Enums\ChildEvaluation\ConductRating;
use App\Enums\ChildEvaluation\EvaluationStatus;
use App\Enums\Semester\SemesterStatus;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ChildEvaluationInfoResource extends JsonResource
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
        $detail = $this->resource['detail'];
        $system = $this->resource['systems'];
        return [
            'detail' => [
                'child_evaluation' => new ChildEvaluationSearchResource($detail['child_evaluation']),
            ],
            'system' => [
                'classes' => ClassesResource::collection($system['class']),
                'subjects' => SubjectResource::collection($system['subjects']),
                'capabilities' => CapabilityResource::collection($system['capabilities']),
                'qualities' => CapabilityResource::collection($system['qualities']),
                'semester' => SemesterStatus::asSelectArray(),
                'conduct_ratings' => ConductRating::asSelectArrayRemovePending(),
                'capability_status' => EvaluationStatus::asSelectArray(),
                'academic_performance' => AcademicRating::asSelectArrayRemovePending(),
                'quality_status' => EvaluationStatus::asSelectArray()
            ]

        ];
    }


}
