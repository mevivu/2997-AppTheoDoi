<?php

namespace App\Api\V1\Repositories\AppVersion;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface AppVersionRepositoryInterface extends EloquentRepositoryInterface
{
    public function findActiveVersion(string $appType, string $platform);
}
