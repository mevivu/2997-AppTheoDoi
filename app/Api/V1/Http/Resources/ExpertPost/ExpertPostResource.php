<?php

namespace App\Api\V1\Http\Resources\ExpertPost;

use App\Api\V1\Http\Resources\Expert\ExpertResource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ExpertPostResource extends JsonResource
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
            'category_id' => $this->category_id ? (string) $this->category_id : null,
            'category_name' => $this->category?->name,
            'age_group' => $this->ageGroup?->name,
            'reading_time' => $this->reading_time,
            'posted_at' => ($this->posted_at ?? $this->created_at) ? format_datetime($this->posted_at ?? $this->created_at) : null,
            'image_url' => formatImageUrl($this->image),
            'excerpt' => $this->excerpt,
            'expert_quote' => $this->expert_quote,
            'content_html' => $this->content,
            'is_featured' => (int) $this->is_featured,
            'views' => (int) $this->views,
            'author' => $this->expert ? new ExpertResource($this->expert) : null,
        ];
    }
}
