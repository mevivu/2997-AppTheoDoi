<?php

namespace App\Services\Tracking;

use App\Models\User;
use App\Models\UserDailyActivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppSessionTracker implements AppSessionTrackerInterface
{
    /** Giới hạn tối đa thời gian sử dụng được ghi nhận trong 1 ngày: 16 giờ = 57,600 giây */
    public const MAX_DAILY_SECONDS = 57600;

    /** Giới hạn tối đa khoảng delta được phép cộng trong 1 lần heartbeat: 90 giây */
    public const MAX_DELTA_SECONDS = 90;

    /** Khoảng thời gian không có heartbeat tối đa trước khi coi là phiên mới: 5 phút = 300 giây */
    public const MAX_IDLE_SECONDS = 300;

    public function recordHeartbeat(User $user, string $event = 'heartbeat', int $clientSeconds = 60): UserDailyActivity
    {
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $today = $now->toDateString();

        // 1. Lấy hoặc khởi tạo bản ghi hoạt động trong ngày
        $activity = UserDailyActivity::firstOrCreate(
            [
                'user_id' => $user->id,
                'activity_date' => $today,
            ],
            [
                'active_seconds' => 0,
                'session_count' => 0,
                'last_heartbeat_at' => null,
            ]
        );

        $deltaSeconds = 0;
        $incrementSession = 0;

        if ($event === 'start') {
            $incrementSession = 1;
            // Phiên mới vừa bắt đầu: nếu chưa có heartbeat gần đây thì chỉ tính 10s khởi động
            $deltaSeconds = min(max(0, $clientSeconds), 30);
        } else {
            // Heartbeat hoặc End
            $lastHeartbeat = $activity->last_heartbeat_at ? Carbon::parse($activity->last_heartbeat_at) : null;

            if ($lastHeartbeat) {
                $realDiff = $now->diffInSeconds($lastHeartbeat);
                if ($realDiff <= self::MAX_IDLE_SECONDS) {
                    // Tín hiệu liên tục: lấy min giữa thời gian client báo và thời gian thực server đo được
                    $deltaSeconds = min(max(0, $clientSeconds), $realDiff, self::MAX_DELTA_SECONDS);
                } else {
                    // Khoảng cách quá xa (> 5 phút): coi là mở lại app, tính phiên mới
                    $incrementSession = 1;
                    $deltaSeconds = min(max(0, $clientSeconds), 60);
                }
            } else {
                $incrementSession = 1;
                $deltaSeconds = min(max(0, $clientSeconds), 60);
            }
        }

        // Đảm bảo không vượt quá trần 16 tiếng/ngày
        $currentSeconds = (int) $activity->active_seconds;
        if ($currentSeconds + $deltaSeconds > self::MAX_DAILY_SECONDS) {
            $deltaSeconds = max(0, self::MAX_DAILY_SECONDS - $currentSeconds);
        }

        // Cập nhật atomic
        $activity->active_seconds = $currentSeconds + $deltaSeconds;
        $activity->session_count = (int) $activity->session_count + $incrementSession;
        $activity->last_heartbeat_at = $now;
        $activity->save();

        return $activity;
    }
}
