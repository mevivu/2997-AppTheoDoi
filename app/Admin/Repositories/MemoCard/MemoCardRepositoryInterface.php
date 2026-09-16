<?php

namespace App\Admin\Repositories\MemoCard;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface MemoCardRepositoryInterface extends EloquentRepositoryInterface
{
    public function getActiveCardsByTheme(int $themeId);

    public function getCardsByTheme(int $themeId);

    public function updatePosition(array $positions): bool;
}
