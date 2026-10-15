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
    public const PLATFORM_DEV = 'dev';

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
        'enabled_dev' => 'boolean',
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
        return match ($platform) {
            self::PLATFORM_IOS => (bool) $this->enabled_ios,
            self::PLATFORM_DEV => (bool) $this->enabled_dev,
            default => (bool) $this->enabled_android,
        };
    }

    public static function normalizePlatform(?string $platform, ?string $env = null): string
    {
        $target = strtolower((string) ($env ?: $platform));
        if ($target === self::PLATFORM_DEV || in_array($target, ['development', 'debug', 'local', 'staging'])) {
            return self::PLATFORM_DEV;
        }

        return $target === self::PLATFORM_IOS
            ? self::PLATFORM_IOS
            : self::PLATFORM_ANDROID;
    }
}
