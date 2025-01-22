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

    case PQ = 'pq';


    public function badge(): string
    {
        return match ($this) {
            QuestionType::IQ => 'bg-red',
            QuestionType::EQ => 'bg-blue',
            QuestionType::AQ, QuestionType::PQ => 'bg-green',
        };
    }
}
