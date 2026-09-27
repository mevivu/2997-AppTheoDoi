<?php

namespace App\Enums\Lesson;

use App\Supports\Enum;

enum LessonDifficulty: string
{
    use Enum;

    case Easy = 'easy';       // Dễ
    case Medium = 'medium';   // Có trợ giúp / Trung bình
    case Hard = 'hard';       // Khó / Tự lập

    public function label(): string
    {
        return match ($this) {
            self::Easy => 'Dễ',
            self::Medium => 'Có trợ giúp',
            self::Hard => 'Tự lập / Thử thách',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Easy => 'bg-green-lt text-green',
            self::Medium => 'bg-warning-lt text-warning',
            self::Hard => 'bg-danger-lt text-danger',
        };
    }

    public static function getDescription(mixed $value): string
    {
        $case = self::tryFrom($value);
        return $case ? $case->label() : (string) $value;
    }
}
