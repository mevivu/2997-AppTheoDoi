<?php

namespace App\Enums\ChildEvaluation;


use App\Admin\Support\Enum;

enum EvaluationStatus: string
{
    use Enum;

    /** Không Đạt */
    case NotAchieved = 'not_achieved';

    /** Đạt */
    case Achieved = 'achieved';

    /** Tốt */
    case Good = 'good';


    public function badge(): string
    {
        return match ($this) {
            EvaluationStatus::Good => 'bg-green',
            EvaluationStatus::NotAchieved => 'bg-yellow',
            EvaluationStatus::Achieved => 'bg-blue',
        };
    }
}
