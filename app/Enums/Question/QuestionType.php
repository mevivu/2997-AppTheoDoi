<?php

namespace App\Enums\Question;


use App\Admin\Support\Enum;

enum QuestionType: string
{
    use Enum;

    /** IQ */
    case IQ = 'iq';

    /** EQ */
    case EQ = 'eq';

    /** AQ */
    case AQ = 'aq';

    /** Thể chất */
    case PQ = 'pq';


    public function badge(): string
    {
        return match ($this) {
            QuestionType::IQ => 'bg-red',
            QuestionType::EQ => 'bg-blue',
            QuestionType::AQ => 'bg-green',
        };
    }
}
