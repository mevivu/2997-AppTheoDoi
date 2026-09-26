<?php

namespace App\Api\V1\Http\Resources\Video;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class AgeGroupResource extends JsonResource
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
            'name' => $this->name,
            'min_months' => $this->min_months,
            'max_months' => $this->max_months,
            'is_prenatal' => ($this->min_months === null && $this->max_months === null),
            'is_current' => (bool) ($this->is_current ?? false),
            'sort_order' => (int) $this->sort_order,
        ];
    }
}
