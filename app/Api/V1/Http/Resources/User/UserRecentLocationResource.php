<?php

namespace App\Api\V1\Http\Resources\User;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class UserRecentLocationResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return $this->collection->map(function ($data) {

            return [
                'id' => $data->id,
                'user_id' => $data->user_id,
                'start_latitude' => $data->start_latitude,
                'start_longitude' => $data->start_longitude,
                'start_address' => $data->start_address,
                'end_latitude' => $data->end_latitude,
                'end_longitude' => $data->end_longitude,
                'end_address' => $data->end_address,
                'created_at' => format_datetime($data->created_at),
                'updated_at' => format_datetime($data->updated_at),
            ];

        });
    }


}