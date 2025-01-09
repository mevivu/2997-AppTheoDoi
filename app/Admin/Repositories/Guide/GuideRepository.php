<?php

namespace App\Admin\Repositories\Guide;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Guide;

class GuideRepository extends EloquentRepository implements GuideRepositoryInterface
{
    public function getModel(): string
    {
        return Guide::class;
    }
}
