<?php

namespace App\Enums\Package;


use App\Admin\Support\Enum;

enum PackageStatus: string
{
    use Enum;

    case Active = 'active';

    case Draft = 'draft';

    case Deleted = 'deleted';


    public function badge(): string
    {
        return match ($this) {
            PackageStatus::Active => 'bg-green',
            PackageStatus::Deleted => 'bg-red',
            PackageStatus::Draft => '',
        };
    }
}
