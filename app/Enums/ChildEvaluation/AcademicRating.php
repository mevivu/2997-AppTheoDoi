<?php

namespace App\Enums\ChildEvaluation;

use App\Admin\Support\Enum;

enum AcademicRating: string
{
    use Enum;

    /** Tốt */
    case Good = 'good';

    /** Khá */
    case Fair = 'fair';

    /** Đạt */
    case Achieved = 'achieved';

    /** Chưa đạt */
    case NotAchieved = 'not_achieved';

    /** Hoàn thành xuất sắc */
    case Excellent = 'excellent';

    /** Hoàn thành tốt */
    case CompletedGood = 'completed_good';

    /** Hoàn thành */
    case Completed = 'completed';

    /** Chưa hoàn thành */
    case NotCompleted = 'not_completed';

    /** Đang chờ xử lý */
    case Pending = 'pending';

    public function badge(): string
    {
        return match ($this) {
            AcademicRating::Excellent => 'bg-green',
            AcademicRating::Good => 'bg-blue',
            AcademicRating::Fair => 'bg-light-blue',
            AcademicRating::Achieved => 'bg-yellow',
            AcademicRating::NotAchieved => 'bg-orange',
            AcademicRating::CompletedGood => 'bg-teal',
            AcademicRating::Completed => 'bg-cyan',
            AcademicRating::NotCompleted => 'bg-red',
            AcademicRating::Pending => 'bg-gray',
        };
    }

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::Good => 'Tốt',
            self::Fair => 'Khá',
            self::Achieved => 'Đạt',
            self::NotAchieved => 'Chưa đạt',
            self::Excellent => 'Hoàn thành xuất sắc',
            self::CompletedGood => 'Hoàn thành tốt',
            self::Completed => 'Hoàn thành',
            self::NotCompleted => 'Chưa hoàn thành',
            default => 'Đang chờ xử lý',
        };
    }

    public static function asSelectArrayRemovePending(): array
    {
        return collect(self::cases())
            ->reject(function ($case) {
                return $case === self::Pending;
            })
            ->mapWithKeys(function ($case) {
                return [$case->value => $case->getTranslatedName()];
            })->toArray();
    }
}
