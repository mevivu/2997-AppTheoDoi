<?php

namespace App\AES;

use App\Repositories\Setting\SettingRepositoryInterface;

class AESHelper
{
    protected static string $aesSecretKey;

    public static function getAESSecretKey(): string
    {
        $setting = app(SettingRepositoryInterface::class);
        if (!isset(self::$aesSecretKey)) {
            self::$aesSecretKey = $setting->findByField("setting_key", 'aes_secret_key')->plain_value;
        }

        return self::$aesSecretKey;
    }

    /** Mã hoá  */
    public static function encrypt($value, $key = null): bool|string
    {
        $key = $key ?: self::getAESSecretKey();
        return openssl_encrypt($value, 'AES-256-CBC', $key, 0, self::getIv($key));
    }

    /** Giải mã */
    public static function decrypt($value, $key = null): bool|string
    {
        $key = $key ?: self::getAESSecretKey();
        return openssl_decrypt($value, 'AES-256-CBC', $key, 0, self::getIv($key));
    }

    protected static function getIv($key): string
    {
        return str_pad(substr($key, 0, 16), 16, "\0");
    }
}
