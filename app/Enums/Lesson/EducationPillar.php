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
    case HOC_TAP = 'hoc_tap';

    public function label(): string
    {
        return match ($this) {
            self::PQ => 'Thể chất (PQ)',
            self::IQ => 'Trí tuệ (IQ)',
            self::EQ => 'Cảm xúc (EQ)',
            self::AQ => 'Vượt khó (AQ)',
            self::HOC_TAP => 'Học tập',
        };
    }

    public function shortName(): string
    {
        return match ($this) {
            self::PQ => 'Thể chất',
            self::IQ => 'Trí tuệ',
            self::EQ => 'Cảm xúc',
            self::AQ => 'Vượt khó',
            self::HOC_TAP => 'Học tập',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PQ => 'ti ti-run',
            self::IQ => 'ti ti-bulb',
            self::EQ => 'ti ti-heart',
            self::AQ => 'ti ti-mountain',
            self::HOC_TAP => 'ti ti-school',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PQ => '#078A87',
            self::IQ => '#7353CD',
            self::EQ => '#EC3364',
            self::AQ => '#277EBC',
            self::HOC_TAP => '#3295C9',
        };
    }

    public function softColor(): string
    {
        return match ($this) {
            self::PQ => '#D1F4EC',
            self::IQ => '#E5DEFF',
            self::EQ => '#FFDDE6',
            self::AQ => '#DDEEFF',
            self::HOC_TAP => '#FFF0C9',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::PQ => 'Giúp bé phát triển thể lực, vận động và thói quen sống lành mạnh',
            self::IQ => 'Khơi mở tư duy, khả năng ghi nhớ và sáng tạo',
            self::EQ => 'Nuôi dưỡng cảm xúc tích cực và sự đồng cảm',
            self::AQ => 'Rèn luyện sự tự tin, kiên trì và khả năng thích nghi',
            self::HOC_TAP => 'Xây dựng niềm vui học hỏi, kỹ năng và phương pháp học tập mỗi ngày',
        };
    }

    public function imageUrl(): string
    {
        return match ($this) {
            self::PQ => asset('assets/images/pillars/pillar_pq.png'),
            self::IQ => asset('assets/images/pillars/pillar_iq.png'),
            self::EQ => asset('assets/images/pillars/pillar_eq.png'),
            self::AQ => asset('assets/images/pillars/pillar_aq.png'),
            self::HOC_TAP => asset('assets/images/pillars/pillar_hoc_tap.png'),
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::PQ => 'bg-blue-lt text-blue',
            self::IQ => 'bg-warning-lt text-warning',
            self::EQ => 'bg-pink-lt text-pink',
            self::AQ => 'bg-green-lt text-green',
            self::HOC_TAP => 'bg-purple-lt text-purple',
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
