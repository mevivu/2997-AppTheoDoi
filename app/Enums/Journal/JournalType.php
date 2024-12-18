<?php

namespace App\Enums\Journal;


use App\Admin\Support\Enum;

enum JournalType: string
{
    use Enum;

    case Prescription = 'prescription';

    case Moment = 'moment';


    public function badge(): string
    {
        return match ($this) {
            JournalType::Prescription => 'bg-green',
            JournalType::Moment => 'bg-blue',
        };
    }
}
