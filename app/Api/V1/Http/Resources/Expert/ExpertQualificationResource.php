<?php

namespace App\Api\V1\Http\Resources\Expert;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ExpertQualificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->id,
            'degree_name' => $this->degree_name ?? '',
            'institution' => $this->institution ?? '',
            'graduation_year' => $this->graduation_year ?? '',
            'specialization' => $this->specialization ?? '',
            'sort_order' => (int) ($this->sort_order ?? 0),
        ];
    }
}
