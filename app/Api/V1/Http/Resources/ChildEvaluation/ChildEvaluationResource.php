<?php

namespace App\Api\V1\Http\Resources\ChildEvaluation;

use App\Api\V1\Support\CheckPackage;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ChildEvaluationResource extends JsonResource
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
        $isContentVisible = $this->checkUserPackage($this->created_at);
        return [
            'class_grade_id' => $this->id,
            'child_id' => $this->child_id,
            'class' => [
                'id' => $this->class->id,
                'name' => $this->class->name
            ],
            'semester1_grade' => $this->semester1_grade,
            'semester2_grade' => $this->semester2_grade,
            'full_year_grade' => $this->full_year_grade,
            'status' => $this->status,
            'details' => ChildEvaluationSemesterResource::collection($this->evaluations),
            'checked' => $isContentVisible

        ];
    }


}
