<?php

namespace App\Api\V1\Http\Resources\Notification;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ShowNotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'message' => $this->message,
            'status' => $this->status,
            'type' => $this->type,
            'read_at' => format_datetime($this->read_at),
            'created_at' => format_datetime($this->created_at),
        ];
    }
}