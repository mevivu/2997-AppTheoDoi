<?php

namespace App\Enums\Child;


use App\Admin\Support\Enum;

enum ChildStatus: string
{
    use Enum;

    case Active = 'active';
    case Draft = 'draft';
    case Deleted = 'deleted';


    public function badge(): string
    {
        return match ($this) {
            ChildStatus::Active => 'bg-green',
            ChildStatus::Deleted => 'bg-red',
            ChildStatus::Draft => '',
        };
    }
}
