<?php

namespace App\Api\V1\Http\Resources\ChildEvaluation;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ChildEvaluationSemesterResource extends JsonResource
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
            'child_evaluation_id' => $this->id,
            'semester' => $this->semester,
            'average_score' => $this->average_score,
            'academic_performance' => $this->academic_performance,
            'calculated_academic_performance' => $this->calculated_academic_performance,
            'calculation_status' => $this->calculation_status,
            'is_performance_overridden' => (bool) $this->is_performance_overridden,
            'conduct' => $this->conduct,
            'status' => $this->status

        ];
    }


}
