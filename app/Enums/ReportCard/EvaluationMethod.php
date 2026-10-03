<?php

namespace App\Enums\ReportCard;

use App\Admin\Support\Enum;

enum EvaluationMethod: string
{
    use Enum;

    case Score = 'score';
    case Comment = 'comment';
    case Level = 'level';
    case LevelWithScore = 'level_with_score';

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::Score => 'Điểm số',
            self::Comment => 'Đánh giá bằng nhận xét',
            self::Level => 'Mức đạt được',
            self::LevelWithScore => 'Mức đạt được & Điểm KTĐK',
        };
    }
}
