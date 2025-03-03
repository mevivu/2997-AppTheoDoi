<?php

namespace App\Api\V1\Http\Resources\Guide;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use App\Api\V1\Http\Resources\Step\StepResource;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class GuideResource extends JsonResource
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
            'description' => $this->description,
            'note' => $this->note,
            'type' => $this->type ,
            'steps' => StepResource::collection($this->steps),
        ];
    }
}
