<?php

namespace App\Api\V1\Http\Resources\Product;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'link' => $this->link,
            'image' => formatImageUrl($this->image),
            'brand_name' => $this->brand ? $this->brand->name : null,
            'product_catalog_name' => $this->productCatalogs->pluck('name')->toArray(),
        ];
    }
}
