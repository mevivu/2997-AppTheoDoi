<?php

namespace App\Enums\Lesson;

use App\Supports\Enum;

/**
 * Mức đánh giá độ khó bài học từ phụ huynh.
 *
 * Lưu ý: Enum này KHÁC với LessonDifficulty (easy/medium/hard) dùng cho Admin.
 * Enum này phục vụ đánh giá chủ quan của phụ huynh sau khi dạy bé.
 */
enum LessonDifficultyRating: string
{
    use Enum;

    case Easy = 'easy';          // Dễ
    case WithHelp = 'with_help'; // Có trợ giúp
    case Hard = 'hard';          // Khó

    public function label(): string
    {
        return match ($this) {
            self::Easy => 'Dễ',
            self::WithHelp => 'Có trợ giúp',
            self::Hard => 'Khó',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Easy => '😊',
            self::WithHelp => '👋',
            self::Hard => '🔥',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Easy => 'bg-green-lt text-green',
            self::WithHelp => 'bg-warning-lt text-warning',
            self::Hard => 'bg-danger-lt text-danger',
        };
    }
}
