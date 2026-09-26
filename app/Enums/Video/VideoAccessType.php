<?php

namespace App\Enums\Video;

use App\Supports\Enum;

enum VideoAccessType: string
{
    use Enum;

    case FREE = 'free';
    case VIP = 'vip';

    public function badge(): string
    {
        return match ($this) {
            self::FREE => 'bg-green',
            self::VIP => 'bg-yellow',
        };
    }
}
