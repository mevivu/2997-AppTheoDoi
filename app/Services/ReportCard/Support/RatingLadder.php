<?php

namespace App\Services\ReportCard\Support;

class RatingLadder
{
    public const RANK_GOOD = 3;
    public const RANK_FAIR = 2;
    public const RANK_ACHIEVED = 1;
    public const RANK_NOT_ACHIEVED = 0;

    public static function rankToRating(int $rank): string
    {
        return match ($rank) {
            self::RANK_GOOD => 'good',
            self::RANK_FAIR => 'fair',
            self::RANK_ACHIEVED => 'achieved',
            default => 'not_achieved',
        };
    }

    public static function ratingToRank(string $rating): int
    {
        return match ($rating) {
            'good' => self::RANK_GOOD,
            'fair' => self::RANK_FAIR,
            'achieved' => self::RANK_ACHIEVED,
            default => self::RANK_NOT_ACHIEVED,
        };
    }

    public static function getLabel(string $rating): string
    {
        return match ($rating) {
            'good' => 'Tốt',
            'fair' => 'Khá',
            'achieved' => 'Đạt',
            'not_achieved' => 'Chưa đạt',
            'excellent' => 'Hoàn thành xuất sắc',
            'completed_good' => 'Hoàn thành tốt',
            'completed' => 'Hoàn thành',
            'not_completed' => 'Chưa hoàn thành',
            default => $rating,
        };
    }
}
