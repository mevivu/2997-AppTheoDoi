<?php

namespace App\Admin\Repositories\AppVersion;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Repositories\AppVersion\AppVersionRepositoryInterface;
use App\Models\AppVersion;

class AppVersionRepository extends EloquentRepository implements AppVersionRepositoryInterface
{
    protected $select = [];

    public function getModel(): string
    {
        return AppVersion::class;
    }
}
