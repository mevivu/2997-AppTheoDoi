<?php

namespace App\Enums\Exercise;

use App\Supports\Enum;

enum ExerciseTopic: string
{
    use Enum;

    case PQ = 'pq';
    case IQ = 'iq';
    case EQ = 'eq';
    case AQ = 'aq';
    case THAI_GIAO = 'thai_giao';

    public function badge(): string
    {
        return match ($this) {
            self::PQ => 'bg-azure',
            self::IQ => 'bg-blue',
            self::EQ => 'bg-pink',
            self::AQ => 'bg-yellow',
            self::THAI_GIAO => 'bg-green',
        };
    }
}
