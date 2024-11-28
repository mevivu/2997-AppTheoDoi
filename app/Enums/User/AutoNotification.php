<?php

namespace App\Enums\User;


use App\Admin\Support\Enum;

enum AutoNotification: int
{
    use Enum;

    case Auto = 1;
    case Off = 2;
}
