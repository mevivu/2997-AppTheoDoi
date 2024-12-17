<?php

namespace App\Api\V1\Http\Resources\Child;


use Illuminate\Http\Resources\Json\ResourceCollection;

class ChildResourceCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'children' => $this->collection->map(function ($item) {
                return new ChildResource($item);
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
