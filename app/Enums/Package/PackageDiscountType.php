<?php

namespace App\Enums\Package;

use App\Supports\Enum;

enum PackageDiscountType: string
{
    use Enum;

    case None = 'none';
    case Percent = 'percent';
    case Fixed = 'fixed';

    public function badge(): string
    {
        return match ($this) {
            self::None => 'bg-secondary-lt',
            self::Percent => 'bg-success-lt',
            self::Fixed => 'bg-primary-lt',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::None => 'Không giảm giá',
            self::Percent => 'Giảm theo phần trăm (%)',
            self::Fixed => 'Giảm theo số tiền (VNĐ)',
        };
    }
}
