<?php

namespace App\Services\Tracking;

use App\Models\User;
use App\Models\UserDailyActivity;

interface AppSessionTrackerInterface
{
    /**
     * Ghi nhận tín hiệu hoạt động (heartbeat / start / end) của ứng dụng
     *
     * @param User $user
     * @param string $event 'start' | 'heartbeat' | 'end'
     * @param int $clientSeconds Số giây đã trôi qua kể từ lần heartbeat trước (mặc định 60s)
     * @return UserDailyActivity
     */
    public function recordHeartbeat(User $user, string $event = 'heartbeat', int $clientSeconds = 60): UserDailyActivity;
}
