<?php

namespace App\Api\V1\Http\Resources\Auth;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class AuthResource extends JsonResource
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
            'code' => $this->code,
            'username' => $this->username,
            'fullname' => $this->fullname,
            'slug' => $this->slug,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'gender' => $this->gender,
            'active' => $this->active,
            'lgn' => $this->longitude,
            'lat' => $this->latitude,
            'birthday' => $this->birthday,
            'avatar' => formatImageUrl($this->avatar),
            'notification_preference' => $this->notification_preference,
            'bank_account_number' => $this->bank_account_number,
            'status' => $this->status,
            'created_at' => format_date($this->created_at),
        ];
    }
}