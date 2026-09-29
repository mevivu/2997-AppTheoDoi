<?php

namespace App\Api\V1\Http\Resources\Lesson;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class PillarResource extends JsonResource
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
            'key' => $this->resource['key'] ?? null,
            'name' => $this->resource['name'] ?? null,
            'short_name' => $this->resource['short_name'] ?? null,
            'description' => $this->resource['description'] ?? null,
            'icon' => $this->resource['icon'] ?? null,
            'image_url' => $this->resource['image_url'] ?? null,
            'color' => $this->resource['color'] ?? null,
            'soft_color' => $this->resource['soft_color'] ?? null,
            'active_color' => $this->resource['active_color'] ?? '#196C74',
            'order' => (int) ($this->resource['order'] ?? 1),
            'is_default' => (bool) ($this->resource['is_default'] ?? false),
            'lessons_count' => (int) ($this->resource['lessons_count'] ?? 0),
        ];
    }
}
