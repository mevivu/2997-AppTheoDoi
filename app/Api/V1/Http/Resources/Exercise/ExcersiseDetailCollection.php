<?php

namespace App\Api\V1\Http\Resources\Exercise;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ExcersiseDetailCollection extends JsonResource
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
            'name'=>$this->name,
            'description'=>$this->description,
            'status'=>$this->status,
            'exercise_type'=>$this->exercise_type
        ];
    }
}
