<?php

namespace App\Enums\Brand;

use App\Admin\Support\Enum;

enum BrandStatus: string
{
    use Enum;

    case Active = 'active';
    case Draft = 'draft';
    case Deleted = 'deleted';

    /**
     * Get the badge class for the brand status.
     *
     * @return string
     */
    public function badge(): string
    {
        return match ($this) {
            BrandStatus::Active => 'bg-green',
            BrandStatus::Draft => 'bg-yellow',
            BrandStatus::Deleted => 'bg-red',
        };
    }
    public static function hasValue(string $value): bool
    {
        return in_array($value, self::getValues());
    }

    /**
     * Get the localized string for the status.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            BrandStatus::Active => __('Hoạt động'),
            BrandStatus::Draft => __('Bản nháp'),
            BrandStatus::Deleted => __('Đã xóa'),
        };
    }
}

