<?php

namespace App\Enums\Guide;


use App\Admin\Support\Enum;

enum GuideType: string
{
    use Enum;

    /** Sức mạnh */
    case Strength = 'strength';

    /** Sức bền */
    case Endurance = 'endurance';


    public function badge(): string
    {
        return match ($this) {
            GuideType::Strength => 'bg-green',
            GuideType::Endurance => 'bg-blue',
        };
    }
}
