<?php

namespace App\Enums\Memo;

use App\Supports\Enum;

enum MemoCompetitionEntryStatus: string
{
    use Enum;

    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Abandoned = 'abandoned';
    case Disqualified = 'disqualified';

    public function badge(): string
    {
        return match ($this) {
            self::InProgress => 'bg-warning-lt text-warning',
            self::Completed => 'bg-success-lt text-success',
            self::Abandoned => 'bg-secondary-lt text-secondary',
            self::Disqualified => 'bg-danger-lt text-danger',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::InProgress => 'Đang thi',
            self::Completed => 'Đã hoàn thành',
            self::Abandoned => 'Bỏ dở',
            self::Disqualified => 'Bị loại',
        };
    }
}
