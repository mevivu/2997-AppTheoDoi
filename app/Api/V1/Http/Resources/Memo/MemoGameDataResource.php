<?php

namespace App\Api\V1\Http\Resources\Memo;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemoGameDataResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $theme = $this['theme'];
        $ageConfig = $this['age_config'];
        $cards = $this['cards'] ?? [];

        $cardBack = $theme->card_back;
        if (empty($cardBack)) {
            $firstCard = \App\Models\MemoCard::where('memo_theme_id', $theme->id)
                ->whereNotNull('image')
                ->where('image', '!=', '')
                ->orderBy('id', 'asc')
                ->first();
            $cardBack = $firstCard?->image ?: $theme->icon;
        }

        return [
            'theme' => [
                'id' => $theme->id,
                'name' => $theme->name,
                'code' => $theme->code,
                'age' => (int) ($theme->age ?? 1),
                'card_back' => $cardBack ? asset($cardBack) : null,
            ],
            'age_config' => [
                'id' => $ageConfig->id,
                'name' => $ageConfig->name,
                'rows' => (int) $ageConfig->rows,
                'columns' => (int) $ageConfig->columns,
                'total_cards' => (int) ($ageConfig->rows * $ageConfig->columns),
                'pairs_count' => (int) ($this['needed_pairs'] ?? $ageConfig->pairs_count),
                'total_duration' => (int) ($ageConfig->total_duration ?: 180),
                'total_rounds' => (int) ($ageConfig->total_rounds ?: 3),
                'peek_time' => (int) ($ageConfig->peek_time ?: 3),
                'max_mistakes' => (int) ($ageConfig->max_mistakes ?? 0),
            ],
            'cards' => array_map(function ($card) {
                return (new MemoGameCardResource($card))->resolve();
            }, $cards),
            'needed_pairs' => (int) ($this['needed_pairs'] ?? $ageConfig->pairs_count),
            'available_cards_count' => (int) ($this['available_cards_count'] ?? 0),
            'is_fallback' => (bool) ($this['is_fallback'] ?? false),
        ];
    }
}
