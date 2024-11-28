<?php

namespace App\Admin\Traits;

use App\Enums\Role\Role;

trait Roles
{
    public function getRoleCustomer(): string
    {
        return Role::Customer->value;
    }

    public function getRoleSupperAdmin(): string
    {
        return Role::SupperAdmin->value;
    }

    public function getRoleStaff(): string
    {
        return Role::Staff->value;
    }


}
