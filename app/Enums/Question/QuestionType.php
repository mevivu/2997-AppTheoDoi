<?php

namespace App\Enums\Question;


use App\Admin\Support\Enum;

enum QuestionType: string
{
    use Enum;

    case IQ = 'iq';
    case EQ = 'eq';
    case AQ = 'aq';


    public function badge(): string
    {
        return match ($this) {
            QuestionType::IQ => 'bg-red',
            QuestionType::EQ => 'bg-blue',
            QuestionType::AQ => 'bg-green',
        };
    }
}