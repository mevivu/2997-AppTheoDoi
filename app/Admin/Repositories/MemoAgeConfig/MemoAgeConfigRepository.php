<?php

namespace App\Admin\Repositories\MemoAgeConfig;

use App\Admin\Repositories\EloquentRepository;
use App\Enums\ActiveStatus;
use App\Models\MemoAgeConfig;

class MemoAgeConfigRepository extends EloquentRepository implements MemoAgeConfigRepositoryInterface
{
    public function getModel(): string
    {
        return MemoAgeConfig::class;
    }

    public function getConfigForAge(int $age)
    {
        return $this->model->where('status', ActiveStatus::Active->value)
            ->where('min_age', '<=', $age)
            ->where('max_age', '>=', $age)
            ->first();
    }
}
