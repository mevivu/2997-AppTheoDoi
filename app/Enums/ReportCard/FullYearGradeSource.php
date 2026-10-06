<?php

namespace App\Enums\ReportCard;

use App\Admin\Support\Enum;

enum FullYearGradeSource: string
{
    use Enum;

    case Auto = 'auto';
    case Manual = 'manual';
    case Computed = 'computed';
    case Overridden = 'overridden';

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::Auto, self::Computed => 'Hệ thống tính',
            self::Manual => 'Nhập tay',
            self::Overridden => 'Chủ động điều chỉnh',
        };
    }
}
