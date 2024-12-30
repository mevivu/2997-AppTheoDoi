<?php

namespace App\Enums\Answser;


use App\Admin\Support\Enum;

enum AnswerType: string
{
    use Enum;
    /** Image */
    case Image = 'image';

    case Normal = 'normal';


    public function badge(): string
    {
        return match ($this) {
            AnswerType::Image => 'bg-green',
            AnswerType::Normal => 'bg-blue',
        };
    }
}
