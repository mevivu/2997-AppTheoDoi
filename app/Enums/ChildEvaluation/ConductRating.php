<?php

namespace App\Enums\ChildEvaluation;


use App\Admin\Support\Enum;

enum ConductRating: string
{
    use Enum;

    /** Tốt*/
    case Good = 'good';

    /** Khá  */
    case Fair = 'fair';

    /** Trung bình  */
    case Average = 'average';

    /** Yếu  */
    case Poor = 'poor';

    /** Đang chờ xử lý */
    case Pending = 'pending';


    public function badge(): string
    {
        return match ($this) {
            ConductRating::Good => 'bg-green',
            ConductRating::Fair => 'bg-blue',
            ConductRating::Average => 'bg-yellow',
            ConductRating::Poor => 'bg-red',
            ConductRating::Pending => 'bg-gray'
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
