<?php

namespace App\Admin\Repositories\Quality;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Quality;

class QualityRepository extends EloquentRepository implements QualityRepositoryInterface
{
    public function getModel(): string
    {
        return Quality::class;
    }
}
