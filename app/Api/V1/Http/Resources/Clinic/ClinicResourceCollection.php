<?php

namespace App\Api\V1\Http\Resources\Clinic;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ClinicResourceCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'clinics' => $this->collection->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'address' => $item->address,
                    'hotline' => $item->hotline,
                    'opening_time' => $item->opening_time,
                    'closing_time' => $item->closing_time,
                    'province' => optional($item->province)->name,
                    'ward' => optional($item->ward)->name,
                    'clinic_type' => optional($item->clinicType)->name,
                    'avatar' => $item->avatar ? formatImageUrl($item->avatar) : null
                ];
            }),
            'links' => [
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $this->currentPage(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
                'limit' => $this->perPage(),
                'total' => $this->total(),
                'count' => $this->count(),
                'total_pages' => $this->lastPage(),
            ],
        ];
    }
}
