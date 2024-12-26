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


    public function badge(): string
    {
        return match ($this) {
            AcademicRating::Excellent => 'bg-green',
            AcademicRating::Good => 'bg-blue',
            AcademicRating::Fair => 'bg-light-blue',
            AcademicRating::Average => 'bg-yellow',
            AcademicRating::Poor => 'bg-orange',
            AcademicRating::Fail => 'bg-red'
        };
    }
}
