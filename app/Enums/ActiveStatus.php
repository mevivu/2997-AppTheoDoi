<?php

namespace App\Enums;


use App\Admin\Support\Enum;

enum ActiveStatus: string
{
    use Enum;

    case Active = 'active';
    case Draft = 'draft';
    case Deleted = 'deleted';


    public function badge(): string
    {
        return match ($this) {
            ActiveStatus::Active => 'bg-green',
            ActiveStatus::Deleted => 'bg-red',
            ActiveStatus::Draft => '',
        };
    }
}
