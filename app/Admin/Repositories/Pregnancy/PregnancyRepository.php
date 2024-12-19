<?php

namespace App\Admin\Repositories\Pregnancy;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Pregnancy;

class PregnancyRepository extends EloquentRepository implements PregnancyRepositoryInterface
{
    public function getModel(): string
    {
        return Pregnancy::class;
    }
}
