<?php

namespace App\Enums\Transaction;


use App\Supports\Enum;

enum TransactionType: string
{
    use Enum;

    case Payment = 'payment';
    case Withdraw = 'withdraw';
    case Deposit = 'deposit';

    public function badge(): string
    {
        return match ($this) {
            TransactionType::Payment => 'bg-blue',
            TransactionType::Withdraw => 'bg-purple',
            TransactionType::Deposit => 'bg-green',
        };
    }

    public function label(): string
    {
        return match ($this) {
            TransactionType::Payment => 'Mua gói dịch vụ',
            TransactionType::Withdraw => 'Rút tiền hoa hồng',
            TransactionType::Deposit => 'Nạp tiền vào ví',
        };
    }
}

