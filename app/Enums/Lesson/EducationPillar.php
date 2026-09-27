<?php

namespace App\Enums\Lesson;

use App\Supports\Enum;

enum EducationPillar: string
{
    use Enum;

    case PQ = 'pq';
    case IQ = 'iq';
    case EQ = 'eq';
    case AQ = 'aq';
    case THAI_GIAO = 'thai_giao';

    public function label(): string
    {
        return match ($this) {
            self::PQ => 'Thể chất (PQ)',
            self::IQ => 'Trí tuệ (IQ)',
            self::EQ => 'Cảm xúc (EQ)',
            self::AQ => 'Vượt khó (AQ)',
            self::THAI_GIAO => 'Học tập & Thai giáo',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PQ => 'ti ti-run',
            self::IQ => 'ti ti-bulb',
            self::EQ => 'ti ti-heart',
            self::AQ => 'ti ti-mountain',
            self::THAI_GIAO => 'ti ti-school',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PQ => '#3B82F6',
            self::IQ => '#F59E0B',
            self::EQ => '#EC4899',
            self::AQ => '#10B981',
            self::THAI_GIAO => '#8B5CF6',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::PQ => 'bg-blue-lt text-blue',
            self::IQ => 'bg-warning-lt text-warning',
            self::EQ => 'bg-pink-lt text-pink',
            self::AQ => 'bg-green-lt text-green',
            self::THAI_GIAO => 'bg-purple-lt text-purple',
        };
    }

    public static function getDescription(mixed $value): string
    {
        $case = self::tryFrom($value);
        return $case ? $case->label() : (string) $value;
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
