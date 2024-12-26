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


    public function badge(): string
    {
        return match ($this) {
            ConductRating::Good => 'bg-green',
            ConductRating::Fair => 'bg-blue',
            ConductRating::Average => 'bg-yellow',
            ConductRating::Poor => 'bg-red'
        };
    }
}
