<?php

namespace App\Enums\Package;


use App\Supports\Enum;

enum PackageType: string
{
    use Enum;

    case OneMonth = '1';
    case ThreeMonths = '3';
    case SixMonths = '6';
    case OneYear = '12';
    case Trial = '0';

    public function badge(): string
    {
        return match ($this) {
            self::OneMonth => 'bg-green-lt',
            self::ThreeMonths => 'bg-blue-lt',
            self::SixMonths => 'bg-gray-lt',
            self::OneYear => 'bg-pink-lt',
            self::Trial => 'bg-yellow-lt',
        };
    }

}
