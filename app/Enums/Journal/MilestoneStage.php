<?php

namespace App\Enums\Journal;

use App\Admin\Support\Enum;

enum MilestoneStage: string
{
    use Enum;

    case Newborn = 'newborn';
    case Toddler = 'toddler';
    case Preschool = 'preschool';

    public function label(): string
    {
        return match ($this) {
            self::Newborn => 'Giai đoạn sơ sinh (0–1 tuổi)',
            self::Toddler => 'Giai đoạn tập đi (1–3 tuổi)',
            self::Preschool => 'Giai đoạn mẫu giáo (3–6 tuổi)',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Newborn => 'bg-azure',
            self::Toddler => 'bg-green',
            self::Preschool => 'bg-purple',
        };
    }

    public function ageRangeMonths(): array
    {
        return match ($this) {
            self::Newborn => [0, 12],
            self::Toddler => [12, 36],
            self::Preschool => [36, 72],
        };
    }

    public static function fromAgeMonths(int $months): self
    {
        if ($months < 12) {
            return self::Newborn;
        }
        if ($months < 36) {
            return self::Toddler;
        }
        return self::Preschool;
    }
}
