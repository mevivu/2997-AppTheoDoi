<?php

namespace App\Api\V1\Http\Resources\Rating;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class RatingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     * @throws Exception
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->id,
            'child_id' => $this->child_id,
            'score' => $this->score,
            'description' => $this->description,
            'type' => $this->type,
            'created_at' => format_datetime($this->created_at),
        ];
    }
}
