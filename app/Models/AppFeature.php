<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Cờ bật/tắt chức năng hiển thị trong app, theo từng nền tảng.
 */
class AppFeature extends Model
{
    public const CACHE_KEY = 'app_features_all';

    public const MODE_HIDE = 'hide';
    public const MODE_MAINTENANCE = 'maintenance';

    public const PLATFORM_ANDROID = 'android';
    public const PLATFORM_IOS = 'ios';

    public const GROUPS = [
        'home' => 'Trang chủ',
        'account' => 'Tài khoản',
        'education' => 'Giáo dục',
        'evaluation' => 'Đánh giá toàn diện',
        'utility' => 'Tiện ích',
    ];

    protected $table = 'app_features';

    protected $guarded = [];

    protected $casts = [
        'enabled_android' => 'boolean',
        'enabled_ios' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => static::flushCache());
        static::deleted(fn () => static::flushCache());
    }

    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Toàn bộ cờ (cache). Dùng cho API public.
     */
    public static function allCached()
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return static::query()->orderBy('sort_order')->orderBy('id')->get();
        });
    }

    public function isEnabledFor(string $platform): bool
    {
        return $platform === self::PLATFORM_IOS ? $this->enabled_ios : $this->enabled_android;
    }

    public static function normalizePlatform(?string $platform): string
    {
        return strtolower((string) $platform) === self::PLATFORM_IOS
            ? self::PLATFORM_IOS
            : self::PLATFORM_ANDROID;
    }
}
