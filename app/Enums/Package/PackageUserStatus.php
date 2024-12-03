<?php

namespace App\Enums\Package;


use App\Admin\Support\Enum;

enum PackageUserStatus: string
{
    use Enum;

    case Active = 'active';

    case Expired = 'expired';


    public function badge(): string
    {
        return match ($this) {
            PackageUserStatus::Active => 'bg-green',
            PackageUserStatus::Expired => 'bg-red',

        };
    }
}
