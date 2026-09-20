<?php

namespace App\Admin\Http\Resources\Memo;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemoPlayDataResource extends JsonResource
{
    /**
     * Không bọc data key để giữ tính tương thích cao với response JSON
     *
     * @var string|null
     */
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

        return [
            'success' => true,
            'theme' => [
                'id' => $theme->id,
                'name' => $theme->name,
                'code' => $theme->code,
                'card_back' => $theme->card_back ? asset($theme->card_back) : null,
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
                'max_moves' => (int) ($ageConfig->max_moves ?? 0),
                'max_mistakes' => (int) ($ageConfig->max_moves ?? 0),
            ],
            'cards' => array_map(function ($card) {
                return (new MemoPlayCardResource($card))->resolve();
            }, $cards),
            'available_cards_count' => (int) ($this['available_cards_count'] ?? 0),
            'is_fallback' => (bool) ($this['is_fallback'] ?? false),
        ];
    }
}
