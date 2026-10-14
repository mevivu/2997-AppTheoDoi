<?php

namespace App\Admin\Repositories\IqNorm;

use App\Admin\Repositories\EloquentRepository;
use App\Models\IqNorm;

class IqNormRepository extends EloquentRepository implements IqNormRepositoryInterface
{
    public function getModel(): string
    {
        return IqNorm::class;
    }
}
