<?php

namespace App\Admin\Services\Device;

use App\Models\UserDevice;
use Illuminate\Support\Facades\DB;

class DeviceStatisticsService
{
    /**
     * Lấy thống kê tỷ lệ phần trăm giữa Apple iOS và Google Android từ toàn bộ hệ thống (bảng user_devices)
     *
     * @return array
     */
    public function getPlatformStats(): array
    {
        // Truy vấn trực tiếp theo cột platform (có đánh index)
        $counts = UserDevice::select('platform', DB::raw('count(*) as aggregate'))
            ->whereNotNull('platform')
            ->groupBy('platform')
            ->pluck('aggregate', 'platform')
            ->toArray();

        $iosCount = (int)($counts[UserDevice::PLATFORM_IOS] ?? 0);
        $androidCount = (int)($counts[UserDevice::PLATFORM_ANDROID] ?? 0);

        // Dự phòng cho bất kỳ bản ghi nào chưa có platform
        $unassigned = UserDevice::whereNull('platform')->select('device_name')->get();
        foreach ($unassigned as $d) {
            $name = strtolower(trim($d->device_name ?? ''));
            if (str_contains($name, 'iphone') || str_contains($name, 'ipad') || str_contains($name, 'ipod') || str_contains($name, 'ios') || str_contains($name, 'apple')) {
                $iosCount++;
            } else {
                $androidCount++;
            }
        }

        $total = $iosCount + $androidCount;

        if ($total > 0) {
            $iosPercent = round(($iosCount / $total) * 100, 1);
            $androidPercent = round(100 - $iosPercent, 1);
            $topPlatform = $iosCount >= $androidCount ? 'iOS' : 'Android';
            $topPercent = max($iosPercent, $androidPercent);
        } else {
            $iosPercent = 0;
            $androidPercent = 0;
            $topPlatform = 'iOS';
            $topPercent = 0;
        }

        return [
            'total' => $total,
            'ios' => [
                'name' => 'Apple iOS',
                'short_name' => 'iOS',
                'percent' => $iosPercent,
                'is_top' => $total > 0 && $topPlatform === 'iOS',
            ],
            'android' => [
                'name' => 'Google Android',
                'short_name' => 'Android',
                'percent' => $androidPercent,
                'is_top' => $total > 0 && $topPlatform === 'Android',
            ],
            'top_platform' => $topPlatform,
            'top_percent' => $topPercent,
            'has_data' => $total > 0,
        ];
    }
}
