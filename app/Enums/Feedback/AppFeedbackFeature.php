<?php

namespace App\Enums\Feedback;

use App\Admin\Support\Enum;

enum AppFeedbackFeature: string
{
    use Enum;

    case HEIGHT_WEIGHT = 'Theo dõi chiều cao, cân nặng.';
    case BODY_MIND_WISDOM = 'Đánh giá Thân – Tâm – Trí.';
    case VIDEO_LESSONS = 'Video và bài học phát triển cho trẻ.';
    case HEALTH_VACCINATION = 'Theo dõi sức khỏe, lịch tiêm chủng.';
    case OTHER = 'Tính năng khác.';

    public static function options(): array
    {
        return [
            self::HEIGHT_WEIGHT->value,
            self::BODY_MIND_WISDOM->value,
            self::VIDEO_LESSONS->value,
            self::HEALTH_VACCINATION->value,
            self::OTHER->value,
        ];
    }

    public static function asSelectArray(): array
    {
        return [
            self::HEIGHT_WEIGHT->value => self::HEIGHT_WEIGHT->value,
            self::BODY_MIND_WISDOM->value => self::BODY_MIND_WISDOM->value,
            self::VIDEO_LESSONS->value => self::VIDEO_LESSONS->value,
            self::HEALTH_VACCINATION->value => self::HEALTH_VACCINATION->value,
            self::OTHER->value => self::OTHER->value,
        ];
    }
}
