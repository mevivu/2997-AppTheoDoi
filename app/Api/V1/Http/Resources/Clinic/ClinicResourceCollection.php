<?php

namespace App\Api\V1\Http\Resources\Clinic;

use App\Api\V1\Http\Resources\Notification\NotificationResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Carbon\Carbon;
class ClinicResourceCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'Clinic' => $this->collection->map(function ($item) {
                $data = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'address'=>$item->address,
                    'hotline'=>$item->hotline,
                    'opening_time'=>$item->opening_time,
                    'closing_time'=>$item->closing_time,
                    'clinic_type' =>  $item->clinicType->name,
                    'province' =>  $item->province->name,
                    'district'=>$item->district->name,
                    'ward'=>$item->ward->name,
                ];
                return $data;
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
