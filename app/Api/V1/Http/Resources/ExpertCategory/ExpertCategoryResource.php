<?php

namespace App\Api\V1\Http\Resources\ExpertCategory;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ExpertCategoryResource extends JsonResource
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
            'slug' => $this->slug,
            'icon' => ($this->icon && (str_contains($this->icon, '/') || str_contains($this->icon, '.'))) ? formatImageUrl($this->icon) : $this->icon,
            'description' => $this->description ?? '',
        ];
    }
}
