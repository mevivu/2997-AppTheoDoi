<?php

namespace App\Enums\User;

use App\Admin\Support\Enum;

enum KycStatus: string
{
    use Enum;

    /** Chưa gửi hồ sơ xác minh CCCD */
    case NOT_SUBMITTED = 'not_submitted';

    /** Đang chờ Admin duyệt CCCD */
    case PENDING = 'pending';

    /** Đã được Admin phê duyệt xác minh thành công */
    case APPROVED = 'approved';

    /** Bị Admin từ chối phê duyệt (cần cập nhật lại) */
    case REJECTED = 'rejected';

    public function badge(): string
    {
        return match ($this) {
            self::NOT_SUBMITTED => 'bg-secondary-lt text-secondary',
            self::PENDING => 'bg-warning text-white',
            self::APPROVED => 'bg-success text-white',
            self::REJECTED => 'bg-danger text-white',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::NOT_SUBMITTED => __('Chưa gửi'),
            self::PENDING => __('Chờ duyệt'),
            self::APPROVED => __('Đã duyệt'),
            self::REJECTED => __('Từ chối'),
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::NOT_SUBMITTED => 'ti ti-file-upload',
            self::PENDING => 'ti ti-clock-hour-4',
            self::APPROVED => 'ti ti-shield-check',
            self::REJECTED => 'ti ti-shield-x',
        };
    }

    public static function asSelectArray(): array
    {
        return [
            self::PENDING->value => __('Chờ duyệt'),
            self::APPROVED->value => __('Đã duyệt'),
            self::REJECTED->value => __('Từ chối'),
            self::NOT_SUBMITTED->value => __('Chưa gửi'),
        ];
    }
}
