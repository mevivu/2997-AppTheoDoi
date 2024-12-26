<?php

namespace App\Enums\ClassGrade;


use App\Supports\Enum;

enum ClassGradeStatus: string
{
    use Enum;

    case Active = 'active';

    case Draft = 'draft';

    case Deleted = 'deleted';

    public function badge(): string
    {
        return match ($this) {
            ClassGradeStatus::Active => 'bg-blue',
            ClassGradeStatus::Draft => 'bg-orange',
            ClassGradeStatus::Deleted => 'bg-green',
        };;
    }
}
