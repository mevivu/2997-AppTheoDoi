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
            self::Email => 'bg-blue-lt',
            self::Google => 'bg-red-lt',
            self::Apple => 'bg-dark-lt',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Email => 'ti ti-mail',
            self::Google => 'ti ti-brand-google',
            self::Apple => 'ti ti-brand-apple',
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
