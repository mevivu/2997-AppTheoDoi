<?php

namespace App\Enums\ReportCard;

use App\Admin\Support\Enum;

enum CalculationStatus: string
{
    use Enum;

    case Ok = 'ok';
    case Incomplete = 'incomplete';
    case InvalidInput = 'invalid_input';
    case NotApplicable = 'not_applicable';

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::Ok => 'Hợp lệ',
            self::Incomplete => 'Chưa đủ dữ liệu',
            self::InvalidInput => 'Dữ liệu không hợp lệ',
            self::NotApplicable => 'Không áp dụng',
        };
    }
}
