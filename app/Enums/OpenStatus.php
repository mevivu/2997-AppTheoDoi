<?php

namespace App\Enums;


use App\Admin\Support\Enum;

enum OpenStatus: string
{
    use Enum;

    case ON = 'ON';

    case OFF = 'OFF';


    public function badge(): string
    {
        return match ($this) {
            OpenStatus::ON => 'bg-green',
            OpenStatus::OFF => 'bg-yellow',
        };
    }
}
