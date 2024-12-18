<?php

namespace App\Enums\Assessment;


use App\Admin\Support\Enum;

enum AssessmentType: string
{
    use Enum;

    // Đánh giá thể chất
    case PQ = 'PQ';

    // Đánh giá trí tuệ
    case IQ = 'IQ';

    // Đánh giá cảm xúc
    case EQ = 'EQ';

    // Đánh giá học lực
    case GPA = 'GPA';

    // Đánh giá khả năng vượt khó
    case AQ = 'AQ';

    public function badge(): string
    {
        return match ($this) {
            AssessmentType::PQ => 'bg-green',
            AssessmentType::IQ => 'bg-blue',
            AssessmentType::EQ => 'bg-red',
            AssessmentType::GPA => 'bg-purple',
            AssessmentType::AQ => 'bg-yellow',
        };
    }
}
