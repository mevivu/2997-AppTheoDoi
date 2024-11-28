<?php

namespace App\Api\V1\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserConfigurationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'cost_preference' => $this->cost_preference,
            'rating_preference' => $this->rating_preference,
            'discount_preference' => $this->discount_preference,
            'distance_preference' => $this->distance_preference,
            'vehicle_type' => $this->vehicle_type,
            'price_setting_c_car' => $this->price_setting_c_car,
        ];
    }
}
