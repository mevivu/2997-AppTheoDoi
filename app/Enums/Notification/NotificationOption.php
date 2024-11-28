<?php

namespace App\Enums\Notification;

use App\Supports\Enum;

enum NotificationOption: int
{
    use Enum;
    case All = 1;
    case One = 2;
}
