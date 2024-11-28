<?php

namespace App\Enums\Notification;

use App\Supports\Enum;

enum MessageType: string
{
    use Enum;

    case DEPOSIT = 'deposit';
    case WITHDRAW = 'withdraw';
    case UNCLASSIFIED = 'unclassified';
    case PAYMENT = 'payment';
    case REPORT = 'report';
    case TEMPORARY_HOLD = 'temporary_hold';
}
