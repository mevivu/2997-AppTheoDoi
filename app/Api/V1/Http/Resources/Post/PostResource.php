<?php

namespace App\Api\V1\Http\Resources\Post;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class PostResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'image' => formatImageUrl($this->image),
            'is_featured' => $this->is_featured,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'posted_at' => format_datetime($this->posted_at),
        ];
    }
}
