<?php

namespace App\Enums\Journal;

use App\Admin\Support\Enum;

enum MilestoneStage: string
{
    use Enum;

    case Newborn = 'newborn';
    case Toddler = 'toddler';
    case Preschool = 'preschool';
    case PrimarySchool = 'primary_school';
    case SecondarySchool = 'secondary_school';

    public function label(): string
    {
        return match ($this) {
            self::Newborn => 'Giai đoạn sơ sinh (0–1 tuổi)',
            self::Toddler => 'Giai đoạn tập đi (1–3 tuổi)',
            self::Preschool => 'Giai đoạn mẫu giáo (3–6 tuổi)',
            self::PrimarySchool => 'Giai đoạn tiểu học (6–11 tuổi)',
            self::SecondarySchool => 'Giai đoạn trung học (11–18 tuổi)',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::Newborn => 'Sơ sinh (0–1 tuổi)',
            self::Toddler => 'Tập đi (1–3 tuổi)',
            self::Preschool => 'Mẫu giáo (3–6 tuổi)',
            self::PrimarySchool => 'Tiểu học (6–11 tuổi)',
            self::SecondarySchool => 'Trung học (11–18 tuổi)',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Newborn => 'bg-azure',
            self::Toddler => 'bg-green',
            self::Preschool => 'bg-purple',
            self::PrimarySchool => 'bg-orange',
            self::SecondarySchool => 'bg-indigo',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Newborn => 'ti-baby-bottle',
            self::Toddler => 'ti-walk',
            self::Preschool => 'ti-palette',
            self::PrimarySchool => 'ti-backpack',
            self::SecondarySchool => 'ti-school',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Newborn => 'azure',
            self::Toddler => 'green',
            self::Preschool => 'purple',
            self::PrimarySchool => 'orange',
            self::SecondarySchool => 'indigo',
        };
    }

    public function ageRangeMonths(): array
    {
        return match ($this) {
            self::Newborn => [0, 12],
            self::Toddler => [12, 36],
            self::Preschool => [36, 72],
            self::PrimarySchool => [72, 132],
            self::SecondarySchool => [132, 216],
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
        if ($months < 72) {
            return self::Preschool;
        }
        if ($months < 132) {
            return self::PrimarySchool;
        }
        return self::SecondarySchool;
    }
}
