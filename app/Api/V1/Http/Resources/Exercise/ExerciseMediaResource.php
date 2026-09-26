<?php

namespace App\Api\V1\Http\Resources\Exercise;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ExerciseMediaResource extends JsonResource
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
            'media_type' => $this->media_type?->value,
            'media_url' => $this->media_file_url,
            'thumbnail_url' => $this->thumbnail_url,
            'sort_order' => (int) $this->sort_order,
        ];
    }
}
