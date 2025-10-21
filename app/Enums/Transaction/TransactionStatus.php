<?php

namespace App\Enums\Transaction;


use App\Supports\Enum;

enum TransactionStatus: string
{
    use Enum;

    case Pending = 'pending';

    case Confirmed = 'confirmed';

    case Refunded = 'refunded';

    public function badge(): string
    {
        return match ($this) {
            TransactionStatus::Pending => 'bg-blue',
            TransactionStatus::Confirmed => 'bg-orange',
            TransactionStatus::Refunded => 'bg-red',
        };
    }

    public function label(): string
    {
        return match ($this) {
            TransactionStatus::Pending => 'Chờ xử lý',
            TransactionStatus::Confirmed => 'Đã xác nhận',
            TransactionStatus::Refunded => 'Đã hoàn tiền',
        };
    }
}

