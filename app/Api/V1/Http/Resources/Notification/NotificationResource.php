<?php

namespace App\Api\V1\Http\Resources\Notification;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class NotificationResource extends JsonResource
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
            'user' => $this->userInfo($this->user),
            'title' => $this->title,
            'status' => $this->status,
            'message' => $this->message,
        ];
    }

    private function userInfo($user)
    {
        return [
            'name' => $user->fullname,
            'image' => formatImageUrl($user->avatar),
        ];
    }
}
