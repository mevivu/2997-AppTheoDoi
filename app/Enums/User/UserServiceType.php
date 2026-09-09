<?php

namespace App\Enums\User;

use App\Admin\Support\Enum;

enum UserServiceType: int
{
    use Enum;

    /** Đăng ký tài khoản qua Email thông thường */
    case Email = 1;

    /** Đăng nhập / Đăng ký qua Google */
    case Google = 2;

    /** Đăng nhập / Đăng ký qua Apple ID */
    case Apple = 3;

    public function badge(): string
    {
        return match ($this) {
            self::Email => 'bg-blue',
            self::Google => 'bg-red',
            self::Apple => 'bg-black',
        };
    }

    public function name(): string
    {
        return match ($this) {
            self::Email => 'Email',
            self::Google => 'Google',
            self::Apple => 'Apple',
        };
    }
}
