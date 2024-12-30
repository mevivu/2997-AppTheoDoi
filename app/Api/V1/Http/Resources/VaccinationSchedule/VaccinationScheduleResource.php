<?php

namespace App\Api\V1\Http\Resources\VaccinationSchedule;

use Illuminate\Http\Resources\Json\JsonResource;

class VaccinationScheduleResource extends JsonResource
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
            'child_id' => $this->child_id,
            'name' => $this->name,
            'description' => $this->description,
            'vaccination_status' => $this->vaccination_status,
            'performed_on' => $this->performed_on ? $this->performed_on->format('Y-m-d') : null,
            'image' => $this->image ? json_decode($this->image) : null,
        ];
    }
}
