<?php

namespace App\Api\V1\Services\AppVersion;

use App\Api\V1\Repositories\AppVersion\AppVersionRepositoryInterface;

class AppVersionService implements AppVersionServiceInterface
{
    protected AppVersionRepositoryInterface $repository;

    public function __construct(AppVersionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function checkVersion(string $appType, string $platform, ?string $currentVersion): array
    {
        $versionInfo = $this->repository->findActiveVersion($appType, $platform);

        return [
            'notify' => $versionInfo ? $versionInfo->notify : $currentVersion,
            'required' => $versionInfo ? $versionInfo->required : $currentVersion,
            'checking_version' => $versionInfo ? $versionInfo->checking_version : null,
            'update_url' => $versionInfo ? $versionInfo->update_url : null,
            'release_notes' => $versionInfo ? $versionInfo->release_notes : null,
        ];
    }
}
