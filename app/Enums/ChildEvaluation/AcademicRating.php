<?php

namespace App\Enums\ChildEvaluation;


use App\Admin\Support\Enum;

enum AcademicRating: string
{
    use Enum;
    /** Xuất sắc */
    case Excellent = 'excellent';
    /** Giỏi */
    case Good = 'good';

    /** Khá */
    case Fair = 'fair';

    /** Trung bình */
    case Average = 'average';

    /** Yếu */
    case Poor = 'poor';

    /** Kém */
    case Fail = 'fail';

    /** Đang chờ xử lý */
    case Pending = 'pending';


    public function badge(): string
    {
        return match ($this) {
            AcademicRating::Excellent => 'bg-green',
            AcademicRating::Good => 'bg-blue',
            AcademicRating::Fair => 'bg-light-blue',
            AcademicRating::Average => 'bg-yellow',
            AcademicRating::Poor => 'bg-orange',
            AcademicRating::Fail => 'bg-red',
            AcademicRating::Pending => 'bg-gray'
        };
    }
    public function getTranslatedName(): string
    {
        return match ($this) {
            self::Excellent => 'Xuất sắc',
            self::Good => 'Giỏi',
            self::Fair => 'Khá',
            self::Average => 'Trung bình',
            self::Poor => 'Yếu',
            self::Fail => 'Kém',
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
