<?php

namespace App\Api\V1\Http\Resources\Rating;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RatingCollection extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'ratings' => $this->collection->map(function ($item) {
                return [
                    'child_id' => $item->child_id,
                    'tag' => $item->tag,
                    'type' => $item->type,
                    'social_awareness' => $item->social_awareness,
                    'relationship_management' => $item->relationship_management,
                    'decision_making' => $item->decision_making,
                    'optimism' => $item->optimism,
                    'endurance' => $item->endurance,
                    'flexibility' => $item->flexibility,
                    'perseverance' => $item->perseverance,
                    'positivity' => $item->positivity,
                    'self_reflection' => $item->self_reflection,
                    'score' => $item->score,
                    'description' => $item->description,
                    'label' => $item->label,
                    'updated_at' => $item->updated_at,
                    'created_at' => $item->created_at
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
