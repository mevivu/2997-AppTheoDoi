<?php

namespace App\Enums;


use App\Admin\Support\Enum;

enum Random: string
{
    use Enum;

    case YES = 'yes';


    public function badge(): string
    {
        return match ($this) {
            Random::YES => 'bg-green',
        };
    }
}
