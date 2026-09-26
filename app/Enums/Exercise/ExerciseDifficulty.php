<?php

namespace App\Enums\Exercise;

use App\Supports\Enum;

enum ExerciseDifficulty: string
{
    use Enum;

    case EASY = 'easy';
    case MEDIUM = 'medium';
    case HARD = 'hard';
    case ASSISTED = 'assisted';

    public function badge(): string
    {
        return match ($this) {
            self::EASY => 'bg-green',
            self::MEDIUM => 'bg-blue',
            self::HARD => 'bg-red',
            self::ASSISTED => 'bg-purple',
        };
    }
}
