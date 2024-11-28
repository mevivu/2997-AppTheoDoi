<?php

namespace App\Api\V1\Repositories\Setting;
use App\Admin\Repositories\EloquentRepositoryInterface;

interface SettingRepositoryInterface extends EloquentRepositoryInterface
{
    public function getByGroup(array $group);
}