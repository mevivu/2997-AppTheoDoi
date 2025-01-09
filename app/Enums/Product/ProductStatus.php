<?php

namespace App\Enums\Product;

use App\Admin\Support\Enum;

enum ProductStatus: string
{
    use Enum;

    case Active = 'active';
    case Draft = 'draft';
    case Deleted = 'deleted';

    public function badge(): string
    {
        return match ($this) {
            ProductStatus::Active => 'bg-green',
            ProductStatus::Draft => 'bg-yellow',
            ProductStatus::Deleted => 'bg-red',
        };
    }
        public static function asSelectArray(): array
        {
            return [
                self::Active->value => 'Kích hoạt',
                self::Draft->value => 'Bản nháp',
                self::Deleted->value => 'Đã xóa',
            ];
        }
    public static function asString($value): string
    {
        return (string) $value;
    }

}
