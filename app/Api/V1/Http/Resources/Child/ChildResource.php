<?php

namespace App\Api\V1\Http\Resources\Child;

use App\Api\V1\Http\Resources\Auth\AuthResource;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ChildResource extends JsonResource
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
            'fullname' => $this->fullname,
            'is_born' => $this->is_born,
            'gender' => $this->gender,
            'birthday' => format_date($this->birthday),
            'avatar' => formatImageUrl($this->avatar),
            'user' => [
                'fullname' => $this->user->fullname
            ]
        ];
    }


}
