<?php

namespace App\Admin\Repositories\UserPackage;

use App\Admin\Repositories\EloquentRepository;
use App\Models\UserPackage;

class UserPackageRepository extends EloquentRepository implements UserPackageRepositoryInterface
{
    public function getModel(): string
    {
        return UserPackage::class;
    }
}
