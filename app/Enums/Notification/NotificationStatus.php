<?php

namespace App\Enums\Notification;

use App\Supports\Enum;

enum NotificationStatus: int
{
    use Enum;


    case READ = 2;
    case NOT_READ = 1;
    public function badge()
    {
        return match ($this) {
            NotificationStatus::NOT_READ => '',
            NotificationStatus::READ => 'bg-green',

        };
    }


}
