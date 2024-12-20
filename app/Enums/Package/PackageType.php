<?php

namespace App\Enums\Package;


use App\Supports\Enum;
use DateInterval;

enum PackageType: string
{
    use Enum;

    case OneMonth = '1';
    case ThreeMonths = '3';
    case SixMonths = '6';
    case OneYear = '12';
    case Trial = '0';

    case Normal = 'normal';

    public function badge(): string
    {
        return match ($this) {
            self::OneMonth => 'bg-green-lt',
            self::ThreeMonths => 'bg-blue-lt',
            self::SixMonths => 'bg-gray-lt',
            self::OneYear => 'bg-pink-lt',
            self::Trial => 'bg-yellow-lt',
            self::Normal => 'bg-orange-lt',
        };
    }

    public function duration(): DateInterval
    {
        return match ($this) {
            self::OneMonth => new DateInterval('P1M'),
            self::ThreeMonths => new DateInterval('P3M'),
            self::SixMonths => new DateInterval('P6M'),
            self::OneYear => new DateInterval('P1Y'),
            self::Trial => new DateInterval('P14D'),
            default => new DateInterval('P30D'),
        };
    }

}
