<?php

namespace App\Enums\Exercise;

use App\Supports\Enum;

enum ExerciseType: string
{
    use Enum;

    case PHYSICAL = 'physical';
    case POWER = 'power';

    public function badge()
    {
        return match ($this) {
            ExerciseType::PHYSICAL => 'bg-blue',
            ExerciseType::POWER => 'bg-green',
        };
    }
}