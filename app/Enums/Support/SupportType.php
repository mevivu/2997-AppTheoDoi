<?php

namespace App\Enums\Support;

use App\Admin\Support\Enum;

enum SupportType: string
{
    use Enum;

    case HelpCenter = 'help_center';
    case Guide = 'guide';

    public function badge(): string
    {
        return match ($this) {
            SupportType::HelpCenter => 'bg-green-lt',
            SupportType::Guide => 'bg-blue-lt',
        };
    }
}
