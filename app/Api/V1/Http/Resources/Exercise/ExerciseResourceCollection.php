<?php

namespace App\Api\V1\Http\Resources\Exercise;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ExerciseResourceCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'Exercise' => $this->collection->map(function ($item) {
                $data = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'status' => $item->status,
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
