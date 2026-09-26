<?php

namespace App\Api\V1\Http\Resources\Video;

use App\Enums\Video\VideoAccessType;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $isLocked = (bool) ($this->is_locked ?? false);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'youtube_id' => $isLocked ? null : $this->youtube_id,
            'video_url' => $isLocked ? null : $this->video_url,
            'thumbnail_url' => $this->thumbnail_url,
            'video_type' => $this->video_type?->value ?? 'youtube',
            'duration_seconds' => $this->duration_seconds,
            'access_type' => $this->access_type?->value,
            'access_type_label' => $this->access_type ? VideoAccessType::getDescription($this->access_type->value) : null,
            'is_preview' => (bool) $this->is_preview,
            'is_locked' => $isLocked,
            'view_count' => (int) $this->view_count,
            'video_category_id' => $this->video_category_id,
            'category_name' => $this->category?->name,
            'sort_order' => (int) $this->sort_order,
            'created_at' => format_datetime($this->created_at),
        ];
    }
}
