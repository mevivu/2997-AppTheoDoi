<?php

namespace App\Enums\Introduction;

use App\Admin\Support\Enum;

enum IntroductionSectionType: int
{
    use Enum;

    case General = 1;
    case Vision = 2;
    case Mission = 3;
    case CoreValue = 4;
    case BrandInfo = 5;

    public function badge(): string
    {
        return match ($this) {
            IntroductionSectionType::General => 'bg-blue-lt',
            IntroductionSectionType::Vision => 'bg-purple-lt',
            IntroductionSectionType::Mission => 'bg-green-lt',
            IntroductionSectionType::CoreValue => 'bg-yellow-lt',
            IntroductionSectionType::BrandInfo => 'bg-cyan-lt',
        };
    }

    public function label(): string
    {
        return match ($this) {
            IntroductionSectionType::General => 'Giới thiệu chung',
            IntroductionSectionType::Vision => 'Tầm nhìn',
            IntroductionSectionType::Mission => 'Sứ mệnh',
            IntroductionSectionType::CoreValue => 'Giá trị cốt lõi',
            IntroductionSectionType::BrandInfo => 'Thương hiệu & Pháp lý',
        };
    }

    public static function asSelectArray(): array
    {
        $array = [];
        foreach (self::cases() as $item) {
            $array[$item->value] = $item->label();
        }
        return $array;
    }
}
