<?php

namespace App\Admin\Repositories\ChildQuality;

use App\Admin\Repositories\EloquentRepository;
use App\Models\ChildQuality;

class ChildQualityRepository extends EloquentRepository implements ChildQualityRepositoryInterface
{
    public function getModel(): string
    {
        return ChildQuality::class;
    }
}
