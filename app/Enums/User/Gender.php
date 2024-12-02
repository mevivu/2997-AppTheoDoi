<?php

namespace App\Enums\User;


use App\Supports\Enum;

enum Gender: int
{
    use Enum;

    case Male = 1;
    case Female = 2;
    case Other = 3;

    public function badge(): string
    {
        return match ($this) {
            self::Male => 'bg-green-lt',
            self::Female => 'bg-blue-lt',
            self::Other => 'bg-gray-lt',
        };
    }

}
