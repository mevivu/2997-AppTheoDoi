<?php

namespace App\Enums\User;


use App\Admin\Support\Enum;

enum UserActive: int
{
    use Enum;

    /** Trạng thái Chờ xác nhận */
    case Active = 1;


    public function badge(): string
    {
        return match ($this) {
            self::Active => 'bg-green',
        };
    }

}
