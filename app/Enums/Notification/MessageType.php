<?php

namespace App\Enums\Notification;

use App\Supports\Enum;

enum MessageType: string
{
    use Enum;

    case UNCLASSIFIED = 'unclassified';
    case PAYMENT = 'payment';

}
