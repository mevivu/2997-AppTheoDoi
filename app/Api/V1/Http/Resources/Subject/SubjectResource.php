<?php

namespace App\Api\V1\Http\Resources\Subject;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class SubjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status->value,
        ];

        if ($this->pivot) {
            $data['evaluation_method'] = $this->pivot->evaluation_method;
            $data['is_required'] = (bool) ($this->pivot->is_required ?? true);
            $data['sort_order'] = $this->pivot->sort_order;
        }

        return $data;
    }
}
