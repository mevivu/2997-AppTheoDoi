<?php

namespace App\Enums\SubjectGrade;


use App\Supports\Enum;

enum AchievementLevel: string
{
    use Enum;

    case Excellent = 'excellent';       // Hoàn thành tốt
    case Completed = 'completed';       // Hoàn thành
    case NotCompleted = 'not_completed'; // Chưa hoàn thành

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::Excellent => 'Hoàn thành tốt',
            self::Completed => 'Hoàn thành',
            self::NotCompleted => 'Chưa hoàn thành',
        };
    }

    public static function asSelectArrayTranslate(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getTranslatedName()])
            ->toArray();
    }

    public function badge(): string
    {
        return match ($this) {
            self::Excellent => 'bg-green',
            self::Completed => 'bg-blue',
            self::NotCompleted => 'bg-red',
        };
    }

}
