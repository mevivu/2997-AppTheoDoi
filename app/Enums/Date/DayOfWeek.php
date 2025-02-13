<?php

namespace App\Enums\Date;


use App\Admin\Support\Enum;

enum DayOfWeek: int
{
    use Enum;

    case Sunday = 1;
    case Monday = 2;
    case Tuesday = 3;
    case Wednesday = 4;
    case Thursday = 5;
    case Friday = 6;
    case Saturday = 7;

    public function label(): string
    {
        return match ($this) {
            DayOfWeek::Sunday => __('Chủ nhật'),
            DayOfWeek::Monday => __('Thứ 2'),
            DayOfWeek::Tuesday => __('Thứ 3'),
            DayOfWeek::Wednesday => __('Thứ 4'),
            DayOfWeek::Thursday => __('Thứ 5'),
            DayOfWeek::Friday => __('Thứ 6'),
            DayOfWeek::Saturday => __('Thứ 7'),
        };
    }
}
