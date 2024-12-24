<?php

namespace App\Api\V1\Http\Resources\Classes;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ClassesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     * @throws Exception
     */
    public function toArray($request): array
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }


}
