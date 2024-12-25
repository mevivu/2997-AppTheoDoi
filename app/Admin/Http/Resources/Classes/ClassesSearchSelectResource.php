<?php

namespace App\Admin\Http\Resources\Classes;

use Illuminate\Http\Resources\Json\JsonResource;
use App\AES\AESHelper;

class ClassesSearchSelectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'text' => $this->name
        ];
    }
}