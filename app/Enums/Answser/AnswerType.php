<?php

namespace App\Enums\Answser;


use App\Admin\Support\Enum;

enum AnswerType: string
{
    use Enum;
    /** Image */
    case Normal = 'normal';

    case Image = 'image';

    public function badge(): string
    {
        return match ($this) {
            AnswerType::Image => 'bg-green',
            AnswerType::Normal => 'bg-blue',
        };
    }
}
