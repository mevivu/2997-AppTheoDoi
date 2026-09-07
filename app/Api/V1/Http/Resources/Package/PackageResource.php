<?php

namespace App\Api\V1\Http\Resources\Package;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class PackageResource extends JsonResource
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
            'name' => $this->name,
            'price' => (float) $this->price,
            'description' => json_decode($this->description),
            'type' => $this->type,
            'code' => $this->code,
            'discount_type' => $this->discount_type?->value ?? 'none',
            'discount_value' => (float) ($this->discount_value ?? 0),
            'discount_amount' => (float) ($this->discount_amount ?? 0),
            'discount_code' => $this->discount_code,
            'final_price' => (float) $this->final_price,
            'has_discount' => (bool) $this->has_discount,
        ];
    }
}
