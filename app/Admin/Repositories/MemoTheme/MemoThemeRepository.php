<?php

namespace App\Admin\Repositories\MemoTheme;

use App\Admin\Repositories\EloquentRepository;
use App\Enums\ActiveStatus;
use App\Models\MemoTheme;

class MemoThemeRepository extends EloquentRepository implements MemoThemeRepositoryInterface
{
    public function getModel(): string
    {
        return MemoTheme::class;
    }

    public function getActiveThemes()
    {
        return $this->model->withCount('cards')
            ->where('status', ActiveStatus::Active->value)
            ->orderBy('position', 'asc')
            ->get();
    }

    public function getAllByPosition()
    {
        return $this->model->withCount('cards')
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
