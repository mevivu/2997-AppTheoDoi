<?php

namespace App\Enums\User;


use App\Admin\Support\Enum;

enum UserStatus: int
{
    use Enum;

    /** Trạng thái Chờ xác nhận */
    case PendingConfirmation = 1;

    /** Trạng thái khoá */
    case Lock = 2;

    /**  Trạng thái hoạt động */
    case Active = 3;

    /**  Trạng thái không hoạt động */
    case Inactive = 4;

    public function badge(): string
    {
        return match ($this) {
            self::Lock => 'bg-red',
            self::PendingConfirmation => 'bg-yellow',
            self::Active => 'bg-green',
            self::Inactive => 'bg-orange',
        };
    }

}
