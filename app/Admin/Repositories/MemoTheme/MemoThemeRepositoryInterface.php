<?php

namespace App\Admin\Repositories\MemoTheme;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface MemoThemeRepositoryInterface extends EloquentRepositoryInterface
{
    public function getActiveThemes();

    public function getAllByPosition();

    public function updatePosition(array $positions): bool;
}
