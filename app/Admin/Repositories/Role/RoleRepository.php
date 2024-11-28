<?php

namespace App\Admin\Repositories\Role;
use App\Admin\Repositories\EloquentRepository;
use App\Admin\Repositories\Role\RoleRepositoryInterface;
use App\Enums\Permission\PermissionType;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Module;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository extends EloquentRepository implements RoleRepositoryInterface
{

    protected $select = [];

    public function getModel()
    {
        return Role::class;
    }


    public function getQueryBuilderOrderBy($column = 'id', $sort = 'DESC')
    {
        $this->getQueryBuilder();
        $this->instance = $this->instance->orderBy($column, $sort);
        return $this->instance;
    }

    public function getAllPermissions(): Collection
    {
        return Permission::all();
    }

    public function getAllPermissionsNoModule($type = null)
    {
        if ($type == PermissionType::USER->value) {
            return Permission::all()->whereNull('module_id')->where('type', PermissionType::USER->value);
        }

        return Permission::all()->whereNull('module_id');
    }

    public function getAllModules(): Collection
    {
        return Module::all();
    }

    public function getAllPermissionsInAllModules($type = null): array
    {
        if ($type == PermissionType::USER->value) {
            $arrModule = Module::whereHas('permissions', function ($query) {
                $query->where('type', PermissionType::USER->value);
            })->orderBy('position')->get();
        } else {
            $arrModule = Module::orderBy('position')->get();
        }

        $permissionsList = [];

        foreach ($arrModule as $module) {
            $permissionsList[$module->id]['module_name'] = $module->name;

            if ($type == PermissionType::USER->value) {
                $permissionsList[$module->id]['list'] = Permission::where('module_id', $module->id)->where('type', PermissionType::USER->value)->get();
            } else {
                $permissionsList[$module->id]['list'] = Permission::where('module_id', $module->id)->get();
            }

        }

        return $permissionsList;
    }
}