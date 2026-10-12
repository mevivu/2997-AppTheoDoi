<?php

namespace App\Services\Assessment;

use App\Api\V1\Repositories\Answer\AnswerRepositoryInterface;
use App\Enums\Question\QuestionType;

class CategoryScoreCalculator
{
    /**
     * Tính toán điểm các nhóm năng lực cho bài kiểm tra EQ / AQ
     *
     * @param array $answers Danh sách câu trả lời [{question_id, answer_id}, ...]
     * @param string|QuestionType $type Loại bài kiểm tra (eq, aq)
     * @param AnswerRepositoryInterface $answerRepository
     * @return array{
     *     score: float,
     *     category_scores: array,
     *     raw_columns: array
     * }
     */
    public static function calculateEqAq(
        array $answers,
        string|QuestionType $type,
        AnswerRepositoryInterface $answerRepository
    ): array {
        $typeKey = $type instanceof QuestionType ? $type->value : strtolower((string) $type);
        $categoriesConfig = config("assessment.categories.{$typeKey}", []);
        $weakThreshold = (float) config('assessment.weak_threshold', 6.0);

        // Tạo map từ group_type -> config của category
        $groupTypeMap = [];
        foreach ($categoriesConfig as $catKey => $conf) {
            $groupTypeMap[$conf['group_type']] = $conf;
        }

        // Gom điểm các câu trả lời theo group_type
        $groupedScores = [];
        foreach ($answers as $item) {
            $ansId = (int) ($item['answer_id'] ?? 0);
            if (!$ansId) {
                continue;
            }

            try {
                $answer = $answerRepository->findOrFail($ansId);
                $question = $answer->question;
                if (!$question || !$question->group) {
                    continue;
                }

                $gt = is_object($question->group->type)
                    ? $question->group->type->value
                    : (string) $question->group->type;

                // Thang điểm Likert 1-5 nhân 2 -> thang điểm 2-10
                $rawScore = ((float) ($answer->score ?? 0)) * 2;
                $groupedScores[$gt][] = $rawScore;
            } catch (\Throwable $e) {
                continue;
            }
        }

        $rawColumns = [];
        $categoryScores = [];
        $totalGroupScores = [];

        foreach ($categoriesConfig as $catKey => $conf) {
            $gt = $conf['group_type'];
            $col = $conf['rating_column'];
            $scores = $groupedScores[$gt] ?? [];

            if (!empty($scores)) {
                $avgScore = round(array_sum($scores) / count($scores), 1);
                $isWeak = $avgScore <= $weakThreshold;

                $rawColumns[$col] = (int) round($avgScore);
                $totalGroupScores[] = $avgScore;

                $categoryScores[$catKey] = [
                    'key' => $catKey,
                    'rating_column' => $col,
                    'label' => $conf['label'],
                    'pillar' => $conf['pillar'],
                    'lesson_category_key' => $conf['lesson_category_key'],
                    'score' => $avgScore,
                    'raw' => "{$avgScore}/10",
                    'is_weak' => $isWeak,
                ];
            } else {
                $rawColumns[$col] = null;
                $categoryScores[$catKey] = [
                    'key' => $catKey,
                    'rating_column' => $col,
                    'label' => $conf['label'],
                    'pillar' => $conf['pillar'],
                    'lesson_category_key' => $conf['lesson_category_key'],
                    'score' => null,
                    'raw' => null,
                    'is_weak' => false,
                ];
            }
        }

        // Tổng điểm trung bình của các nhóm đánh giá (thang điểm 10)
        $finalScore = !empty($totalGroupScores)
            ? round(array_sum($totalGroupScores) / count($totalGroupScores), 1)
            : 0.0;

        return [
            'score' => $finalScore,
            'category_scores' => $categoryScores,
            'raw_columns' => $rawColumns,
        ];
    }

    /**
     * Xây dựng snapshot category_scores cho bài kiểm tra IQ từ group totals và group corrects
     *
     * @param array $groupTotals Tổng số câu/lượt chơi của từng nhóm năng lực
     * @param array $groupCorrects Số câu đúng/lượt thắng của từng nhóm năng lực
     * @return array
     */
    public static function calculateIq(array $groupTotals, array $groupCorrects): array
    {
        $categoriesConfig = config('assessment.categories.' . QuestionType::IQ->value, []);
        $weakThreshold = (float) config('assessment.weak_threshold', 6.0);
        $categoryScores = [];

        foreach ($categoriesConfig as $catKey => $conf) {
            $col = $conf['rating_column'];
            $tot = (int) ($groupTotals[$col] ?? 0);
            $cor = (int) ($groupCorrects[$col] ?? 0);

            if ($tot > 0) {
                $scaledScore = round(($cor / $tot) * 10, 1);
                $isWeak = $scaledScore <= $weakThreshold;

                $categoryScores[$catKey] = [
                    'key' => $catKey,
                    'rating_column' => $col,
                    'label' => $conf['label'],
                    'pillar' => $conf['pillar'],
                    'lesson_category_key' => $conf['lesson_category_key'],
                    'score' => $scaledScore,
                    'raw' => "{$cor}/{$tot}",
                    'is_weak' => $isWeak,
                ];
            } else {
                $categoryScores[$catKey] = [
                    'key' => $catKey,
                    'rating_column' => $col,
                    'label' => $conf['label'],
                    'pillar' => $conf['pillar'],
                    'lesson_category_key' => $conf['lesson_category_key'],
                    'score' => null,
                    'raw' => null,
                    'is_weak' => false,
                ];
            }
        }

        return $categoryScores;
    }
}
