<?php

namespace App\Admin\Repositories\FetalGrowthStandard;

use App\Admin\Repositories\EloquentRepository;
use App\Models\FetalGrowthStandard;

class FetalGrowthStandardRepository extends EloquentRepository implements FetalGrowthStandardRepositoryInterface
{
    public function getModel(): string
    {
        return FetalGrowthStandard::class;
    }
}
