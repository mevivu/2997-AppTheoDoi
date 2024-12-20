<?php

namespace App\Enums\Package;


use App\Admin\Support\Enum;

enum PackageUserStatus: string
{
    use Enum;

    case Active = 'active';

    case Pending = 'pending';


    public function badge(): string
    {
        return match ($this) {
            PackageUserStatus::Active => 'bg-green',
            PackageUserStatus::Pending => 'bg-red',

        };
    }
}
