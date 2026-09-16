<?php

namespace App\Admin\Repositories\MemoAgeConfig;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface MemoAgeConfigRepositoryInterface extends EloquentRepositoryInterface
{
    public function getConfigForAge(int $age);
}
