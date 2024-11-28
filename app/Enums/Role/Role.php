<?php

namespace App\Enums\Role;


use App\Admin\Support\Enum;

enum Role: string
{
    use Enum;

    case Customer = "customer";
    case SupperAdmin = "superAdmin";
    case Staff = "staff";

    public function badge(): string
    {
        return match ($this) {
            Role::Customer => 'bg-green-lt',
            Role::SupperAdmin => 'bg-pink-lt',
            Role::Staff => 'bg-yellow-lt'
        };
    }
}
