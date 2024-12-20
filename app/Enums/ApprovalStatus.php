<?php

namespace App\Enums;

use App\Supports\Enum;

enum ApprovalStatus: string
{
    use Enum;

    case PENDING = 'pending';
    case ACTIVE = 'active';
    case REJECTED = 'rejected';

    public function badge(): string
    {
        return match ($this) {
            ApprovalStatus::PENDING => 'bg-yellow',
            ApprovalStatus::ACTIVE => 'bg-green',
            ApprovalStatus::REJECTED => 'bg-red',
        };
    }
}
