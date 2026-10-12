<?php

namespace App\Services\Assessment;

use App\Enums\Question\QuestionType;

class AssessmentBandResolver
{
    /**
     * Xác định bậc điểm, nhãn xếp loại, nhận định và hành động cho bố mẹ
     *
     * @param string|QuestionType $type Loại bài kiểm tra (iq, eq, aq)
     * @param float|int|string|null $score Điểm số (thang 10)
     * @return array{band: string, label: string, description: string, parent_action: string}
     */
    public static function resolve(string|QuestionType $type, float|int|string|null $score): array
    {
        $typeKey = $type instanceof QuestionType ? $type->value : strtolower((string) $type);
        $numericScore = is_null($score) ? 0.0 : max(0.0, min(10.0, (float) $score));

        $bands = config("assessment.bands.{$typeKey}", []);

        if (empty($bands)) {
            return [
                'band' => 'B0',
                'label' => 'Chưa đánh giá',
                'description' => 'Chưa có thông tin nhận định cho kết quả này.',
                'parent_action' => 'Hãy tiếp tục đồng hành và theo dõi sự phát triển của con.',
            ];
        }

        foreach ($bands as $band) {
            $maxScore = (float) ($band['max_score'] ?? 10.0);
            if ($numericScore <= $maxScore) {
                return [
                    'band' => $band['band'] ?? 'B1',
                    'label' => $band['label'] ?? '',
                    'description' => $band['description'] ?? '',
                    'parent_action' => $band['parent_action'] ?? '',
                ];
            }
        }

        // Nếu vượt quá mức cao nhất (ví dụ > 9.0 đến 10.0)
        $lastBand = end($bands);
        return [
            'band' => $lastBand['band'] ?? 'B5',
            'label' => $lastBand['label'] ?? '',
            'description' => $lastBand['description'] ?? '',
            'parent_action' => $lastBand['parent_action'] ?? '',
        ];
    }
}
