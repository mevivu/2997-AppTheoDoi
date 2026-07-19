<?php

namespace App\Api\V1\Services\AppVersion;

interface AppVersionServiceInterface
{
    public function checkVersion(string $appType, string $platform, ?string $currentVersion): array;
}
