<?php
namespace App\Api\V1\Http\Resources\Step;

use Illuminate\Http\Resources\Json\JsonResource;

class StepResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'order' => $this->order,
            'title' => $this->title,
            'description' => $this->description,
        ];
    }
}
