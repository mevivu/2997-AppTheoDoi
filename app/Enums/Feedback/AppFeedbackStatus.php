<?php

namespace App\Enums\Feedback;

use App\Admin\Support\Enum;

enum AppFeedbackStatus: string
{
    use Enum;

    /** Chờ duyệt */
    case PENDING = 'pending';

    /** Đã duyệt */
    case APPROVED = 'approved';

    /** Đã cấp VIP */
    case VIP_GRANTED = 'vip_granted';

    /** Từ chối */
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('Chờ duyệt'),
            self::APPROVED => __('Đã duyệt'),
            self::VIP_GRANTED => __('Đã cấp VIP'),
            self::REJECTED => __('Từ chối'),
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::PENDING => 'bg-warning text-white',
            self::APPROVED => 'bg-info text-white',
            self::VIP_GRANTED => 'bg-success text-white',
            self::REJECTED => 'bg-danger text-white',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PENDING => 'ti ti-clock-hour-4',
            self::APPROVED => 'ti ti-circle-check',
            self::VIP_GRANTED => 'ti ti-crown',
            self::REJECTED => 'ti ti-circle-x',
        };
    }

    public static function asSelectArray(): array
    {
        return [
            self::PENDING->value => __('Chờ duyệt'),
            self::APPROVED->value => __('Đã duyệt'),
            self::VIP_GRANTED->value => __('Đã cấp VIP'),
            self::REJECTED->value => __('Từ chối'),
        ];
    }
}
