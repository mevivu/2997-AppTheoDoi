<?php

namespace App\Enums\Class;


use App\Admin\Support\Enum;

enum LevelGroup: string
{
    use Enum;

    case Junior = 'junior'; // Lớp <= 6
    case Senior = 'senior'; // Lớp > 6


    public function badge(): string
    {
        return match ($this) {
            LevelGroup::Junior => 'bg-blue',
            LevelGroup::Senior => 'bg-orange',
        };
    }
}
