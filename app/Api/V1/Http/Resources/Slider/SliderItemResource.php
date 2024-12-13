<?php

namespace App\Api\V1\Http\Resources\Slider;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class SliderItemResource extends JsonResource
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
            'link' => $this->link,
            'position' => $this->position,
            'image' => formatImageUrl($this->image),
            'mobile_image' => formatImageUrl($this->mobile_image),
        ];
    }
}
