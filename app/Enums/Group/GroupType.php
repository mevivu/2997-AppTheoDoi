<?php

namespace App\Enums\Group;


use App\Admin\Support\Enum;

enum GroupType: string
{
    use Enum;

    // Đồng cảm
    case Empathy = 'empathy';
    // Động lực
    case Motivation = 'motivation';
    // Kỹ năng xã hội
    case SocialSkills = 'social_skills';
    // Kiểm soát cảm xúc
    case EmotionalRegulation = 'emotional_regulation';
    // Nhận thức cảm xúc
    case EmotionalAwareness = 'emotional_awareness';

    public function badge(): string
    {
        return match ($this) {
            self::Empathy => 'bg-blue',
            self::Motivation => 'bg-yellow',
            self::SocialSkills => 'bg-green',
            self::EmotionalRegulation => 'bg-orange',
            self::EmotionalAwareness => 'bg-purple',
        };
    }
}
