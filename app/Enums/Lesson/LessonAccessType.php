<?php

namespace App\Enums\Lesson;

use App\Supports\Enum;

enum LessonAccessType: string
{
    use Enum;

    case Free = 'free'; // Miễn phí cho mọi tài khoản
    case Vip = 'vip';   // Dành riêng cho gói VIP

    public function label(): string
    {
        return match ($this) {
            self::Free => 'Miễn phí',
            self::Vip => 'Gói VIP',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Free => 'bg-green-lt text-green',
            self::Vip => 'bg-yellow text-dark',
        };
    }

    public static function getDescription(mixed $value): string
    {
        $case = self::tryFrom($value);
        return $case ? $case->label() : (string) $value;
    }
}
