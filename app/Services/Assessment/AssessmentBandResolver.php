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
        $rawNumericScore = is_null($score) ? 0.0 : (float) $score;

        // Nếu là bài test IQ và điểm số > 10 -> Áp dụng Thang phân loại Wechsler quốc tế (Thang điểm 100)
        if ($typeKey === 'iq' && $rawNumericScore > 10.0) {
            $wechslerBands = config('assessment.wechsler_bands', []);
            foreach ($wechslerBands as $wBand) {
                $maxScore = (float) ($wBand['max_score'] ?? 999.0);
                if ($rawNumericScore <= $maxScore) {
                    return [
                        'band' => $wBand['band'] ?? 'W4',
                        'label' => $wBand['label'] ?? '',
                        'description' => $wBand['description'] ?? '',
                        'parent_action' => $wBand['parent_action'] ?? '',
                    ];
                }
            }
            $lastWBand = end($wechslerBands);
            return [
                'band' => $lastWBand['band'] ?? 'W7',
                'label' => $lastWBand['label'] ?? '',
                'description' => $lastWBand['description'] ?? '',
                'parent_action' => $lastWBand['parent_action'] ?? '',
            ];
        }

        $numericScore = max(0.0, min(10.0, $rawNumericScore));
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
