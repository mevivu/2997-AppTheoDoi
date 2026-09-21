<?php

namespace App\Admin\Services\Device;

use App\Models\UserDevice;

class DeviceStatisticsService
{
    /**
     * Lấy thống kê tỷ lệ phần trăm giữa Apple iOS và Google Android từ toàn bộ hệ thống (bảng user_devices)
     *
     * @return array
     */
    public function getPlatformStats(): array
    {
        $devices = UserDevice::select('id', 'device_name')->get();

        $iosKeywords = ['iphone', 'ipad', 'ipod', 'ios', 'apple'];
        $iosCount = 0;
        $androidCount = 0;

        foreach ($devices as $device) {
            $name = strtolower(trim($device->device_name ?? ''));
            $isIos = false;

            foreach ($iosKeywords as $kw) {
                if (str_contains($name, $kw)) {
                    $isIos = true;
                    break;
                }
            }

            if ($isIos) {
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
