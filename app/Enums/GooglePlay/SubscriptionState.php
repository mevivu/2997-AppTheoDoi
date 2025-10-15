<?php

namespace App\Enums\GooglePlay;

use App\Supports\Enum;

enum SubscriptionState: string
{
    use Enum;

    /** Đang hoạt động (đã thanh toán và còn hạn) */
    case ACTIVE = 'SUBSCRIPTION_STATE_ACTIVE';

    /** Người dùng đã hủy (sẽ hết hạn vào cuối kỳ hiện tại) */

    case CANCELED = 'SUBSCRIPTION_STATE_CANCELED';

    /** Đã hết hạn */
    case EXPIRED = 'SUBSCRIPTION_STATE_EXPIRED';

    /** Google đang giữ (ví dụ do lỗi thanh toán) */
    case ON_HOLD = 'SUBSCRIPTION_STATE_ON_HOLD';

    /** Gia hạn tạm thời do lỗi thanh toán */
    case IN_GRACE_PERIOD = 'SUBSCRIPTION_STATE_IN_GRACE_PERIOD';

    /** Người dùng tạm dừng thuê bao */

    case PAUSED = 'SUBSCRIPTION_STATE_PAUSED';


    public function badge(): string
    {
        return match ($this) {
            SubscriptionState::ACTIVE => 'bg-blue',
            SubscriptionState::ON_HOLD => 'bg-green',
            SubscriptionState::IN_GRACE_PERIOD => 'bg-yellow',
            SubscriptionState::PAUSED => 'bg-orange',
            SubscriptionState::CANCELED, SubscriptionState::EXPIRED => 'bg-red',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [
            self::ACTIVE,
            self::IN_GRACE_PERIOD,
        ], true);
    }

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Đang hoạt động',
            self::CANCELED => 'Đã hủy',
            self::EXPIRED => 'Hết hạn',
            self::ON_HOLD => 'Tạm giữ',
            self::IN_GRACE_PERIOD => 'Gia hạn tạm thời',
            self::PAUSED => 'Tạm dừng',

        };
    }
}
