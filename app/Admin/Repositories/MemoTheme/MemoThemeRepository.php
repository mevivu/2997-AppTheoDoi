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
}
