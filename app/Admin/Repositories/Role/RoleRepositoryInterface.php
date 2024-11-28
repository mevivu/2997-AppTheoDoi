<?php

namespace App\Admin\Repositories\Role;
use App\Admin\Repositories\EloquentRepositoryInterface;

interface RoleRepositoryInterface extends EloquentRepositoryInterface
{
    public function getQueryBuilderOrderBy($column = 'id', $sort = 'DESC');
    public function getAllPermissions();
    public function getAllPermissionsNoModule($type = null);
    public function getAllModules();
    public function getAllPermissionsInAllModules($type = null);
}