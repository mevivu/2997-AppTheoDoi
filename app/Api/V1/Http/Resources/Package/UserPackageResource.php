<?php

namespace App\Api\V1\Http\Resources\Package;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class UserPackageResource extends JsonResource
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
            'id' => $this->package->id,
            'start_date' => format_datetime($this->start_date),
            'end_date' => format_datetime($this->end_date),
            'type' => $this->current_type
        ];
    }
}
