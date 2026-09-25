<?php

namespace App\Enums\Memo;

use App\Supports\Enum;

enum MemoConfigType: string
{
    use Enum;

    case IqTest = 'iq_test';
    case Competition = 'competition';

    public function badge(): string
    {
        return match ($this) {
            self::IqTest => 'bg-info-lt text-info',
            self::Competition => 'bg-primary-lt text-primary',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::IqTest => 'Bài kiểm tra IQ',
            self::Competition => 'Giải đấu',
        };
    }

    public function description(): string
    {
        return $this->label();
    }

    public static function asSelectArray(): array
    {
        $array = [];
        foreach (self::cases() as $item) {
            $array[$item->value] = $item->label();
        }
        return $array;
    }
}
