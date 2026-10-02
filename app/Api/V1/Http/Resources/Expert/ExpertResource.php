<?php

namespace App\Api\V1\Http\Resources\Expert;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ExpertResource extends JsonResource
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
            'council_type' => $this->council_type?->value ?? (int) $this->council_type,
            'council_name' => $this->council_type?->label() ?? 'Hội đồng Tư vấn Chuyên môn',
            'name' => $this->name,
            'title' => $this->title,
            'hospital' => $this->hospital ?? '',
            'workplace' => $this->workplace ?? $this->hospital ?? '',
            'avatar' => formatImageUrl($this->avatar),
            'bio' => $this->bio ?? '',
            'contact_link' => $this->contact_link ?? '',
            'contact_phone' => $this->contact_phone ?? '',
            'is_verified' => (bool) $this->is_verified,
            'qualifications' => ExpertQualificationResource::collection($this->qualifications ?? []),
        ];
    }
}
