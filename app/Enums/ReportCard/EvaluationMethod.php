<?php

namespace App\Enums\ReportCard;

use App\Admin\Support\Enum;
use App\Enums\Class\EducationLevel;

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
            self::Comment => 'Đạt (Đ) / Chưa đạt (CĐ)',
            self::Level => 'Mức đạt được (T/H/C)',
            self::LevelWithScore => 'Mức đạt được & Điểm KTĐK',
        };
    }

    /**
     * Phương thức hợp lệ theo cấp học (TT27 tiểu học, TT22 THCS/THPT).
     *
     * @return self[]
     */
    public static function allowedFor(EducationLevel $level): array
    {
        return $level === EducationLevel::Primary
            ? [self::Level, self::LevelWithScore]
            : [self::Score, self::Comment];
    }

    public static function defaultFor(EducationLevel $level): self
    {
        return $level === EducationLevel::Primary ? self::Level : self::Score;
    }

    /**
     * @return array<string, array{value: string, label: string}[]>
     */
    public static function optionsByEducationLevel(): array
    {
        $result = [];
        foreach (EducationLevel::cases() as $level) {
            $result[$level->value] = array_map(
                fn (self $m) => ['value' => $m->value, 'label' => $m->getTranslatedName()],
                self::allowedFor($level)
            );
        }

        return $result;
    }
}
