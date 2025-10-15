<?php

namespace App\Enums\Transaction;


use App\Supports\Enum;

enum TransactionEnumService: string
{
    use Enum;

    /** Bình thường */
    case NORMAL = 'NORMAL';

    /** Google Play */
    case GOOGLE_PLAY = 'GOOGLE_PLAY';

    /** Apple Store */
    case APPLE = 'APPLE';

    public function badge(): string
    {
        return match ($this) {
            TransactionEnumService::NORMAL => 'bg-blue',
            TransactionEnumService::GOOGLE_PLAY => 'bg-green',
            TransactionEnumService::APPLE => 'bg-gray',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::NORMAL => 'Thanh toán thường',
            self::GOOGLE_PLAY => 'Google Play',
            self::APPLE => 'Apple Store',
        };
    }
}

