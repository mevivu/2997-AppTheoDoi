<?php

namespace App\Enums\Memo;

use App\Supports\Enum;

enum MemoCompetitionStatus: string
{
    use Enum;

    case Draft = 'draft';
    case Upcoming = 'upcoming';
    case Active = 'active';
    case Ended = 'ended';
    case Cancelled = 'cancelled';

    public function badge(): string
    {
        return match ($this) {
            self::Draft => 'bg-secondary-lt text-secondary',
            self::Upcoming => 'bg-info-lt text-info',
            self::Active => 'bg-success-lt text-success',
            self::Ended => 'bg-muted-lt text-muted',
            self::Cancelled => 'bg-danger-lt text-danger',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Bản nháp',
            self::Upcoming => 'Sắp diễn ra',
            self::Active => 'Đang diễn ra',
            self::Ended => 'Đã kết thúc',
            self::Cancelled => 'Đã hủy',
        };
    }
}
