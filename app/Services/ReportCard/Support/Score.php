<?php

namespace App\Services\ReportCard\Support;

class Score
{
    /**
     * Chuyển đổi điểm số (float|int|string) sang số nguyên đơn vị phần trăm (1/100).
     * Ví dụ: 7.25 => 725, 8 => 800, "7.5" => 750.
     */
    public static function toHundredths(float|int|string|null $score): ?int
    {
        if ($score === null || $score === '') {
            return null;
        }

        $numeric = (float) $score;
        return (int) round($numeric * 100);
    }

    /**
     * Chuyển đổi điểm số sang số nguyên đơn vị phần mười (1/10).
     */
    public static function toTenths(float|int|string|null $score): ?int
    {
        if ($score === null || $score === '') {
            return null;
        }

        $numeric = (float) $score;
        return (int) round($numeric * 10);
    }

    /**
     * Chuyển từ đơn vị phần trăm sang float chuẩn 2 chữ số.
     */
    public static function fromHundredths(int $hundredths): float
    {
        return round($hundredths / 100, 2);
    }

    /**
     * Chuyển từ đơn vị phần mười sang float chuẩn 1 chữ số.
     */
    public static function fromTenths(int $tenths): float
    {
        return round($tenths / 10, 1);
    }

    /**
     * Tính điểm trung bình môn cả năm theo TT22 Điều 9:
     * ĐTBmcn = (HK1 + 2×HK2) / 3, làm tròn half-up về 1 chữ số thập phân.
     * Đầu vào: điểm HK1 và HK2 ở đơn vị phần trăm (hundredths).
     * Đầu ra: điểm cả năm ở đơn vị phần mười (tenths).
     */
    public static function fullYear(int $hk1Hundredths, int $hk2Hundredths): int
    {
        $sum = $hk1Hundredths + 2 * $hk2Hundredths;
        return intdiv($sum * 10 + 150, 300);
    }

    /**
     * Tính điểm cả năm và trả về float (1 chữ số thập phân).
     */
    public static function formatFullYear(int $hk1Hundredths, int $hk2Hundredths): float
    {
        return self::fromTenths(self::fullYear($hk1Hundredths, $hk2Hundredths));
    }

    /**
     * Tính điểm trung bình cộng của một mảng điểm (đơn vị float).
     */
    public static function average(array $scores, int $decimals = 2): ?float
    {
        $filtered = array_filter($scores, fn($s) => $s !== null && is_numeric($s));
        if (empty($filtered)) {
            return null;
        }

        $avg = array_sum($filtered) / count($filtered);
        return round($avg, $decimals);
    }
}
