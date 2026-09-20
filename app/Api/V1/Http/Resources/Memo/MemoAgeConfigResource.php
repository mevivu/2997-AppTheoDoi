<?php

namespace App\Api\V1\Http\Resources\Memo;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemoAgeConfigResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'min_age' => (int) $this->min_age,
            'max_age' => (int) $this->max_age,
            'rows' => (int) $this->rows,
            'columns' => (int) $this->columns,
            'total_cards' => (int) ($this->rows * $this->columns),
            'pairs_count' => (int) ($this->pairs_count ?: floor(($this->rows * $this->columns) / 2)),
            'total_duration' => (int) ($this->total_duration ?: 540),
            'total_rounds' => (int) ($this->total_rounds ?: 3),
            'peek_time' => (int) ($this->peek_time ?: 3),
            'max_moves' => (int) ($this->max_moves ?? 0),
            'max_mistakes' => (int) ($this->max_moves ?? 0),
        ];
    }
}
