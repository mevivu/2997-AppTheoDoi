<?php

namespace App\Services\ChildScore;

use App\Enums\Question\QuestionType;
use App\Models\Child;
use App\Models\ClassGrade;
use App\Models\Rating;
use App\Models\RatingPQ;
use Throwable;

class ChildScoreAggregator implements ChildScoreAggregatorInterface
{
    /**
     * Lấy điểm mới nhất của cả 5 chỉ số (IQ, EQ, AQ, GPA, PQ) cho một trẻ em
     */
    public function getLatestScores(int $childId): array
    {
        $latestIq = Rating::where('child_id', $childId)
            ->where('type', QuestionType::IQ)
            ->whereNotNull('score')
            ->orderByDesc('age')
            ->orderByDesc('created_at')
            ->first();

        $latestEq = Rating::where('child_id', $childId)
            ->where('type', QuestionType::EQ)
            ->whereNotNull('score')
            ->orderByDesc('created_at')
            ->first();

        $latestAq = Rating::where('child_id', $childId)
            ->where('type', QuestionType::AQ)
            ->whereNotNull('score')
            ->orderByDesc('created_at')
            ->first();

        $latestGpa = ClassGrade::where('child_id', $childId)
            ->whereNotNull('full_year_grade')
            ->orderByDesc('class_id')
            ->first();

        $pqScore = $this->calculatePQScore($childId);

        return [
            'iq' => $latestIq ? (float) $latestIq->score : null,
            'eq' => $latestEq ? (float) $latestEq->score : null,
            'aq' => $latestAq ? (float) $latestAq->score : null,
            'gpa' => $latestGpa ? (float) $latestGpa->full_year_grade : null,
            'pq' => $pqScore,
        ];
    }

    /**
     * Chuẩn hóa điểm thô về thang điểm 0 - 100
     * Cả 5 chỉ số IQ, EQ, AQ, GPA, PQ đều có thang chuẩn 0 - 10 nên nhân 10 để ra thang 0 - 100
     */
    public function normalize(string $type, ?float $raw): ?float
    {
        if (is_null($raw)) {
            return null;
        }

        // Kẹp giá trị trong khoảng 0 - 10 và nhân 10 để đưa về 0 - 100
        $clamped = max(0.0, min(10.0, (float) $raw));
        return round($clamped * 10, 2);
    }

    /**
     * Tính điểm trung bình phát triển tổng hợp của các con thuộc về một phụ huynh (thang 0 - 100)
     */
    public function getAverageNormalizedScoreForUser(int $userId): float
    {
        $childrenIds = Child::where('user_id', $userId)->pluck('id')->toArray();
        if (empty($childrenIds)) {
            return 0.0;
        }

        $allNormalizedScores = [];

        foreach ($childrenIds as $childId) {
            $scores = $this->getLatestScores($childId);
            foreach ($scores as $type => $rawScore) {
                if (!is_null($rawScore)) {
                    $norm = $this->normalize($type, $rawScore);
                    if (!is_null($norm)) {
                        $allNormalizedScores[] = $norm;
                    }
                }
            }
        }

        if (empty($allNormalizedScores)) {
            return 0.0;
        }

        $avg = array_sum($allNormalizedScores) / count($allNormalizedScores);
        return round($avg, 2);
    }

    /**
     * Tính điểm PQ mới nhất an toàn (thang 0 - 10)
     */
    protected function calculatePQScore(int $childId): ?float
    {
        $latest = RatingPQ::where('child_id', $childId)
            ->orderByDesc('assessment_date')
            ->first();

        if (!$latest) {
            return null;
        }

        $age = $latest->child?->age ?? null;
        if (is_null($age)) {
            return null;
        }

        try {
            $currentHeight = floatval($latest->height_result);
            $heightAdulthood = floatval($latest->height_change);
            $bmiPercent = floatval($latest->bmi);
            $strengthPercent = floatval($latest->strength);
            $endurancePercent = floatval($latest->endurance);

            if ($age > 5) {
                $totalScore = $bmiPercent + $endurancePercent + $strengthPercent + $currentHeight + $heightAdulthood;
                return min(round($totalScore / 5, 1), 10.0);
            } else {
                $totalScore = $endurancePercent + $strengthPercent + $currentHeight + $heightAdulthood;
                return min(round($totalScore / 4, 1), 10.0);
            }
        } catch (Throwable $e) {
            return null;
        }
    }
}
