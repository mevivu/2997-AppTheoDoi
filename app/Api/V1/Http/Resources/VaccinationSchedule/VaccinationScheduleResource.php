<?php

namespace App\Api\V1\Http\Resources\VaccinationSchedule;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VaccinationScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'vaccination_status' => $this->vaccination_status,
            'performed_on' => format_date($this->performed_on),
            'image' => $this->image,
        ];
    }
}
