<?php

namespace App\Api\V1\Repositories\AppVersion;

use App\Admin\Repositories\AppVersion\AppVersionRepository as AdminAppVersionRepository;
use App\Api\V1\Repositories\AppVersion\AppVersionRepositoryInterface;

class AppVersionRepository extends AdminAppVersionRepository implements AppVersionRepositoryInterface
{
    public function findActiveVersion(string $appType, string $platform)
    {
        return $this->model->query()
            ->where('app_type', $appType)
            ->where('platform', $platform)
            ->where('is_active', true)
            ->orderBy('id', 'desc')
            ->first();
    }
}
