<?php

namespace App\Admin\Repositories\UserPackage;

use App\Admin\Repositories\EloquentRepository;
use App\Console\Commands\UpdatePackage;

class UserPackageRepository extends EloquentRepository implements UserPackageRepositoryInterface
{
    public function getModel(): string
    {
        return UpdatePackage::class;
    }
}
