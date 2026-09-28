<?php

namespace App\Enums\Lesson;

use App\Supports\Enum;

enum LessonCategoryKey: string
{
    use Enum;

    // 1. Thể chất (PQ)
    case Height = 'height';                             // Chiều cao
    case Bmi = 'bmi';                                   // BMI (Cân nặng & Thể trạng)
    case Strength = 'strength';                         // Sức mạnh
    case Endurance = 'endurance';                       // Sức bền

    // 2. Trí tuệ (IQ)
    case Language = 'language';                         // Ngôn ngữ
    case MathLogic = 'math_logic';                     // Toán học – Logic
    case Visual = 'visual';                             // Hình ảnh & Không gian
    case Memory = 'memory';                             // Trí nhớ

    // 3. Cảm xúc (EQ)
    case EmotionRecognition = 'emotion_recognition';   // Nhận biết cảm xúc
    case Empathy = 'empathy';                           // Đồng cảm
    case SocialCommunication = 'social_communication'; // Giao tiếp xã hội
    case Motivation = 'motivation';                     // Động lực
    case EmotionControl = 'emotion_control';           // Kiểm soát cảm xúc

    // 4. Vượt khó (AQ)
    case Resilience = 'resilience';                     // Khả năng chịu đựng
    case Patience = 'patience';                         // Kiên nhẫn
    case Positivity = 'positivity';                     // Tích cực
    case SelfReflection = 'self_reflection';           // Tự phản hồi
    case Flexibility = 'flexibility';                   // Linh hoạt

    // 5. Thai giáo & Học tập (Prenatal & Learning)
    case PrenatalMusic = 'prenatal_music';             // Âm nhạc thai giáo
    case PrenatalStory = 'prenatal_story';             // Lời thủ thỉ & Kể chuyện
    case PrenatalNutrition = 'prenatal_nutrition';     // Dinh dưỡng thai kỳ
    case PrenatalMovement = 'prenatal_movement';       // Vận động nhẹ nhàng
    case StudyMethod = 'study_method';                 // Phương pháp học tập
    case Focus = 'focus';                               // Rèn luyện tập trung

    case Other = 'other';                               // Khác

    public function label(): string
    {
        return match ($this) {
            self::Height => 'Chiều cao',
            self::Bmi => 'BMI',
            self::Strength => 'Sức mạnh',
            self::Endurance => 'Sức bền',

            self::Language => 'Ngôn ngữ',
            self::MathLogic => 'Toán học – Logic',
            self::Visual => 'Hình ảnh',
            self::Memory => 'Trí nhớ',

            self::EmotionRecognition => 'Nhận biết cảm xúc',
            self::Empathy => 'Đồng cảm',
            self::SocialCommunication => 'Giao tiếp xã hội',
            self::Motivation => 'Động lực',
            self::EmotionControl => 'Kiểm soát cảm xúc',

            self::Resilience => 'Khả năng chịu đựng',
            self::Patience => 'Kiên nhẫn',
            self::Positivity => 'Tích cực',
            self::SelfReflection => 'Tự phản hồi',
            self::Flexibility => 'Linh hoạt',

            self::PrenatalMusic => 'Âm nhạc thai giáo',
            self::PrenatalStory => 'Lời thủ thỉ & Kể chuyện',
            self::PrenatalNutrition => 'Dinh dưỡng thai kỳ',
            self::PrenatalMovement => 'Vận động nhẹ nhàng',
            self::StudyMethod => 'Phương pháp học tập',
            self::Focus => 'Rèn luyện tập trung',

            self::Other => 'Khác',
        };
    }

    public function pillar(): EducationPillar
    {
        return match ($this) {
            self::Height, self::Bmi, self::Strength, self::Endurance, self::PrenatalMovement => EducationPillar::PQ,
            self::Language, self::MathLogic, self::Visual, self::Memory,
            self::PrenatalMusic, self::PrenatalStory, self::PrenatalNutrition, self::StudyMethod, self::Focus => EducationPillar::IQ,
            self::EmotionRecognition, self::Empathy, self::SocialCommunication, self::Motivation, self::EmotionControl => EducationPillar::EQ,
            self::Resilience, self::Patience, self::Positivity, self::SelfReflection, self::Flexibility => EducationPillar::AQ,
            default => EducationPillar::PQ,
        };
    }

    public static function getDescription(mixed $value): string
    {
        $case = self::tryFrom($value);
        return $case ? $case->label() : (string) $value;
    }

    /**
     * Lấy danh sách key theo từng lĩnh vực (Pillar)
     */
    public static function getByPillar(EducationPillar|string $pillar): array
    {
        $targetPillar = is_string($pillar) ? EducationPillar::tryFrom($pillar) : $pillar;
        $result = [];

        foreach (self::cases() as $case) {
            if ($case->pillar() === $targetPillar || $case === self::Other) {
                $result[$case->value] = [
                    'key' => $case->value,
                    'label' => $case->label(),
                ];
            }
        }

        return $result;
    }
}
