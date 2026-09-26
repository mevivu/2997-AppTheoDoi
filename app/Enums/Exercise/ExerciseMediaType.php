<?php

namespace App\Enums\Exercise;

use App\Supports\Enum;

enum ExerciseMediaType: string
{
    use Enum;

    case IMAGE = 'image';
    case VIDEO = 'video';

    public function badge(): string
    {
        return match ($this) {
            self::IMAGE => 'bg-blue',
            self::VIDEO => 'bg-red',
        };
    }
}
