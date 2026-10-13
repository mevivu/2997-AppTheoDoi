<?php

namespace App\Api\V1\Http\Resources\Rating;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class RatingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     * @throws Exception
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $analysis = app(\App\Services\Assessment\AssessmentRecommendationService::class)->buildAnalysis($this->resource);

        return [
            'id' => $this->id,
            'child_id' => $this->child_id,
            'score' => $this->score,
            'result' => $this->result,
            'tag' => $this->tag,
            'description' => $this->description,
            'type' => $this->type,
            'status' => $this->status,
            'label' => $this->label,
            'badge_image' => formatImageUrl($this->badge_image),
            'parent_action' => $this->parent_action ?? ($analysis['parent_action'] ?? null),
            'category_scores' => $this->category_scores ?? ($analysis['categories'] ?? null),
            'analysis' => $analysis,
            // Giữ lại các cột điểm chi tiết cũ để tương thích ngược 100% với App
            'self_regulation' => $this->self_regulation,
            'social_awareness' => $this->social_awareness,
            'relationship_management' => $this->relationship_management,
            'decision_making' => $this->decision_making,
            'optimism' => $this->optimism,
            'endurance' => $this->endurance,
            'flexibility' => $this->flexibility,
            'perseverance' => $this->perseverance,
            'positivity' => $this->positivity,
            'self_reflection' => $this->self_reflection,
            'linguistic' => $this->linguistic,
            'logic_math' => $this->logic_math,
            'visual' => $this->visual,
            'memory' => $this->memory,
            'version' => $this->version,
            'game_score' => $this->game_score,
            'game_duration_spent' => $this->game_duration_spent,
            'game_pairs_matched' => $this->game_pairs_matched,
            'game_mistakes' => $this->game_mistakes,
            'created_at' => format_datetime($this->created_at),
            'updated_at' => format_datetime($this->updated_at),
        ];
    }
}
