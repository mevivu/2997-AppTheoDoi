<?php

namespace App\Api\V1\Http\Resources\Pregnancy;

use App\Api\V1\Support\CheckPackage;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class PregnancyResource extends JsonResource
{
    use CheckPackage;
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     * @throws Exception
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $isContentVisible = $this->checkUserPackage($this->created_at);
        return [
            'id' => $this->id,
            'start_date' => format_date($this->start_date),
            'end_date' => format_date($this->end_date),
            'week' => $this->week,
            'weight' => $this->weight,
            'length' => $this->length,
            'head_circumference' => $this->head_circumference,
            'image' => formatImageUrl($this->image),
            'created_at' => format_date($this->created_at),
            'checked' => $isContentVisible

        ];
    }
}
