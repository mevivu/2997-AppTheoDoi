<?php

namespace App\Enums;


use App\Admin\Support\Enum;

enum VerifiedStatus: string
{
    use Enum;

    case Pending = 'pending';

    case Active = 'active';


    public function badge(): string
    {
        return match ($this) {
            VerifiedStatus::Active => 'bg-green',
            VerifiedStatus::Pending => 'bg-yellow',
        };
    }
}
