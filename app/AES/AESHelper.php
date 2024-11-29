<?php

namespace App\AES;

class AESHelper
{
    protected static string $aesSecretKey;

    public static function getAESSecretKey(): string
    {
        if (!isset(self::$aesSecretKey)) {
            self::$aesSecretKey = env('AES_SECRET_KEY');
        }

        return self::$aesSecretKey;
    }

    /** Mã hoá  */
    public static function encrypt($value): bool|string
    {
        $key = self::getAESSecretKey();
        return openssl_encrypt($value, 'AES-256-CBC', $key, 0, self::getIv());
    }

    /** Giải mã */
    public static function decrypt($value): bool|string
    {
        $key = self::getAESSecretKey();
        return openssl_decrypt($value, 'AES-256-CBC', $key, 0, self::getIv());
    }

    protected static function getIv(): string
    {
        return substr(self::getAESSecretKey(), 0, 16);
    }
}
