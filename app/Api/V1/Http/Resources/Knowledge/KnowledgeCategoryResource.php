<?php

namespace App\Api\V1\Http\Resources\Knowledge;

use Illuminate\Http\Resources\Json\JsonResource;

class KnowledgeCategoryResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'desc' => $this->desc,
            'avatar' => formatImageUrl($this->avatar),
            'position' => $this->position,
            'posts_count' => $this->whenCounted('posts'),
        ];
    }
}
