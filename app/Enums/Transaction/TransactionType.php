<?php

namespace App\Enums\Transaction;


use App\Supports\Enum;

enum TransactionType: string
{
    use Enum;

    case Payment = 'payment';

    public function badge(): string
    {
        return match ($this) {
            TransactionType::Payment => 'bg-blue',
        };
    }
}

