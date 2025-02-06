<?php

namespace App\Enums\Transaction;


use App\Supports\Enum;

enum TransactionStatus: string
{
    use Enum;

    case Pending = 'pending';

    case Confirmed = 'confirmed';



    public function badge(): string
    {
        return match ($this) {
            TransactionStatus::Pending => 'bg-blue',
            TransactionStatus::Confirmed => 'bg-orange',
        };
    }
}

