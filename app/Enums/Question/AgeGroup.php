<?php

namespace App\Enums\Question;


use App\Admin\Support\Enum;

enum AgeGroup: string
{
    use Enum;
    /** Dưới 10 tuổi */
    case Under_10 = 'Under_10';
    /** Trên 10 tuổi */
    case Above_10 = 'Above_10';


    public function badge(): string
    {
        return match ($this) {
            AgeGroup::Under_10 => 'bg-red',
            AgeGroup::Above_10 => 'bg-blue',
        };
    }
}
