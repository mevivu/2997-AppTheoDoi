<?php

namespace App\Api\V1\Http\Resources\Knowledge;

use Illuminate\Http\Resources\Json\JsonResource;

class KnowledgePostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'image' => formatImageUrl($this->image),
            'is_featured' => $this->is_featured?->value ?? (int) $this->is_featured,
            'excerpt' => $this->excerpt,
            'posted_at' => format_datetime($this->posted_at),
            'categories' => KnowledgeCategoryResource::collection($this->whenLoaded('categories')),
        ];
    }
}
