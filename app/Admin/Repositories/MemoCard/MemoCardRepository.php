<?php

namespace App\Admin\Repositories\MemoCard;

use App\Admin\Repositories\EloquentRepository;
use App\Enums\ActiveStatus;
use App\Models\MemoCard;

class MemoCardRepository extends EloquentRepository implements MemoCardRepositoryInterface
{
    public function getModel(): string
    {
        return MemoCard::class;
    }

    public function getActiveCardsByTheme(int $themeId)
    {
        return $this->model->where('memo_theme_id', $themeId)
            ->where('status', ActiveStatus::Active->value)
            ->orderBy('position', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    public function getCardsByTheme(int $themeId)
    {
        return $this->model->where('memo_theme_id', $themeId)
            ->where('status', '!=', ActiveStatus::Deleted->value)
            ->orderBy('position', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    public function updatePosition(array $positions): bool
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($positions) {
            foreach ($positions as $index => $id) {
                $this->model->where('id', $id)->update(['position' => $index + 1]);
            }
            return true;
        });
    }
}
