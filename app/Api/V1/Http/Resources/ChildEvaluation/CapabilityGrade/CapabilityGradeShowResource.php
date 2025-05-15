<?php

namespace App\Api\V1\Http\Resources\ChildEvaluation\CapabilityGrade;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class CapabilityGradeShowResource extends JsonResource
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
            'capability_id' => $this->capability_id,
            'capability_status' => $this->capability_status,
            'remark' => $this->remark,
        ];
    }


}
