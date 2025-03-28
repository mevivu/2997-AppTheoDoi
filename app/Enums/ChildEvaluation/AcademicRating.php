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
    public static function asSelectArrayRemovePending(): array
    {
        return collect(self::cases())
            ->reject(function ($case) {
                return $case === self::Pending;
            })
            ->mapWithKeys(function ($case) {
                return [$case->value => $case->name];
            })->toArray();
    }
}
