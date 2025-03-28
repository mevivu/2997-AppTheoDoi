<?php

namespace App\Enums\ChildEvaluation;


use App\Admin\Support\Enum;

enum EvaluationStatus: string
{
    use Enum;

    /** Không Đạt */
    case NotAchieved = 'not_achieved';

    /** Đạt */
    case Achieved = 'achieved';

    /** Tốt */
    case Good = 'good';


    public function getTranslatedName(): string
    {
        return match ($this) {
            self::NotAchieved => 'Không Đạt',
            self::Achieved => 'Đạt',
            self::Good => 'Tốt',
            default => 'Đang chờ xử lý',
        };
    }
    public static function asSelectArrayTranslate(): array
    {
        return collect(self::cases())
            ->mapWithKeys(function ($case) {
                return [$case->value => $case->getTranslatedName()];
            })->toArray();
    }
    public function badge(): string
    {
        return match ($this) {
            EvaluationStatus::Good => 'bg-green',
            EvaluationStatus::NotAchieved => 'bg-yellow',
            EvaluationStatus::Achieved => 'bg-blue',
        };
    }
}
