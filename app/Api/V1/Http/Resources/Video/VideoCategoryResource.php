<?php

namespace App\Api\V1\Http\Resources\Video;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class VideoCategoryResource extends JsonResource
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
            'icon' => $this->icon,
            'age_group_id' => $this->age_group_id,
            'age_group_name' => $this->ageGroup?->name,
            'sort_order' => (int) $this->sort_order,
            'videos_count' => $this->whenCounted('videos'),
        ];
    }
}
