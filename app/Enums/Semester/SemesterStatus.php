<?php

namespace App\Enums\Semester;


use App\Supports\Enum;

enum SemesterStatus: string
{
    use Enum;

    case Semester1 = 'semester_1';
    case Semester2 = 'semester_2';
    case FullYear = 'full_year';

    public function badge(): string
    {
        return match ($this) {
            self::Semester1 => 'bg-green-lt',
            self::Semester2 => 'bg-blue-lt',
            self::FullYear => 'bg-pink-lt',
        };
    }

}
