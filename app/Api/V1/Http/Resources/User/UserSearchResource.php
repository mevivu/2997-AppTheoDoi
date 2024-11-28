<?php

namespace App\Api\V1\Http\Resources\User;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class UserSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $roles = $this->roles->pluck('name');
        return [
            'id' => $this->id,
            'fullname' => $this->fullname,
        ];
    }
}
