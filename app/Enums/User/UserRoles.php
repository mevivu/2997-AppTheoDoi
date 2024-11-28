<?php

namespace App\Enums\User;

use App\Admin\Support\Enum;

enum UserRoles: int
{
    use Enum;

    case Driver = 1;
    case Customer = 2;

    public function badge(): string
    {
        return match ($this) {
            UserRoles::Driver => 'bg-green-lt',
            UserRoles::Customer => 'bg-red-lt',
        };
    }

    public static function values(): array
    {
        return [
            self::Driver,
            self::Customer,
        ];
    }
}
