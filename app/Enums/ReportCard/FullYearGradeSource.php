<?php

namespace App\Enums\ReportCard;

use App\Admin\Support\Enum;

enum FullYearGradeSource: string
{
    use Enum;

    case Manual = 'manual';
    case Computed = 'computed';
    case Overridden = 'overridden';

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::Manual => 'Nhập tay',
            self::Computed => 'Hệ thống tính',
            self::Overridden => 'Chủ động điều chỉnh',
        };
    }
}
