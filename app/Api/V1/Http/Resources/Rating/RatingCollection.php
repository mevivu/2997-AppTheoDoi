<?php

namespace App\Api\V1\Http\Resources\Rating;

use App\Enums\Question\QuestionType;
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
                if ($item->type == QuestionType::EQ || $item->type == QuestionType::AQ) {
                    return [
                        'child_id' => $item->child_id,
                        'tag' => $item->tag,
                        'type' => $item->type,
                        'social_awareness' => $item->social_awareness,
                        'relationship_management' => $item->relationship_management,
                        'decision_making' => $item->decision_making,
                        'optimism' => $item->optimism,
                        'self_regulation' => $item->self_regulation,
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
                } else {
                    return [
                        'id' => $item->id,
                        'child_id' => $item->child_id,
                        'tag' => $item->tag,
                        'age' => $item->age,
                        'type' => $item->type,
                        'score' => $item->score,
                        'result' => $item->result,
                        'description' => $item->description,
                        'badge_image' => formatImageUrl($item->badge_image),
                        'status' => $item->status,
                        'label' => $item->label,
                        'updated_at' => $item->updated_at,
                        'created_at' => $item->created_at
                    ];
                }
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
