<?php

namespace App\Admin\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use App\AES\AESHelper;

class UserSearchSelectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {   
        $phone = AESHelper::decrypt($this->phone);
        return [
            'id' => $this->id,
            'text' => $this->fullname.' - '.$phone
        ];
    }
}