<?php

namespace App\Enums;


use App\Admin\Support\Enum;

enum FeaturedStatus: int
{
    use Enum;

    case Featured = 1;
    case Featureless = 2;

    public function badge(): string
    {
        return match ($this) {
            FeaturedStatus::Featured => 'bg-green',
            FeaturedStatus::Featureless => 'bg-red',
        };
    }
}
