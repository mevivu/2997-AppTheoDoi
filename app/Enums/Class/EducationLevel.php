<?php

namespace App\Enums\Class;

use App\Admin\Support\Enum;

enum EducationLevel: string
{
    use Enum;

    case Primary = 'primary';
    case LowerSecondary = 'lower_secondary';
    case UpperSecondary = 'upper_secondary';

    public static function fromClassId(int $classId): self
    {
        if ($classId <= 5) {
            return self::Primary;
        }

        if ($classId <= 9) {
            return self::LowerSecondary;
        }

        return self::UpperSecondary;
    }

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::Primary => 'Tiểu học',
            self::LowerSecondary => 'THCS',
            self::UpperSecondary => 'THPT',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Primary => 'bg-green',
            self::LowerSecondary => 'bg-blue',
            self::UpperSecondary => 'bg-purple',
        };
    }
}
