<?php

namespace App\Enums\Video;

use App\Supports\Enum;

enum VideoType: string
{
    use Enum;

    case YouTube = 'youtube';
    case R2 = 'r2';

    public function badge(): string
    {
        return match ($this) {
            self::YouTube => 'bg-danger-lt text-danger',
            self::R2 => 'bg-orange-lt text-orange',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::YouTube => 'ti ti-brand-youtube',
            self::R2 => 'ti ti-cloud-upload',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::YouTube => 'YouTube',
            self::R2 => 'Cloudflare R2',
        };
    }
}
