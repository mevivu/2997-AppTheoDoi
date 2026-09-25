<?php

namespace App\Api\V1\Services\Rating;

use App\Api\V1\Services\Memo\MemoGameBuilderService;
use App\Enums\ActiveStatus;
use App\Enums\Question\QuestionType;
use App\Enums\VerifiedStatus;
use App\Models\MemoAgeConfig;
use App\Models\MemoRating;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RatingServiceV2 extends RatingService implements RatingServiceV2Interface
{
    /**
     * Submit và tính điểm bài kiểm tra IQ V2 (12 câu hỏi + Memo Game)
     *
     * @throws Exception
     */
    public function storeIQV2(Request $request): object
    {
        $data = $request->validated();
        $answers = $data['answers'] ?? [];
        $childId = $data['child_id'];
        $quizId = $data['quiz_id'];
        $quiz = $this->quizRepository->findOrFail($quizId);
        $quizQuestions = $quiz->questions()->with('group')->get();
        $totalQuestionCount = $quizQuestions->count();
        $ratingId = $data['rating_id'];
        $child = $this->childRepository->findOrFail($childId);
        $childName = $child->fullname;
        $type = QuestionType::IQ->value;

        // Kiểm tra cấu hình Memo Game theo độ tuổi hoặc theo ID gửi lên từ app
        $ageConfig = null;
        if (!empty($data['memo_age_config_id'])) {
            $ageConfig = MemoAgeConfig::find($data['memo_age_config_id']);
        }
        if (!$ageConfig) {
            $ageConfig = MemoAgeConfig::forAge((int) ($quiz->age ?? 1))->first();
        }
        if (!$ageConfig) {
            $ageConfig = MemoAgeConfig::where('status', ActiveStatus::Active->value)->orderBy('min_age', 'asc')->first();
        }

        $memoGames = MemoGameBuilderService::buildRounds((int) ($quiz->age ?? 1));
        $hasGame = ($ageConfig !== null) || !empty($memoGames) || !empty($data['memo_theme_id']);

        if (!$hasGame) {
            $gamePlays = 0;
            $gameScore = 0;
        } else {
            // Số lần chơi game lấy theo cấu hình độ tuổi (total_rounds)
            $gamePlays = $ageConfig ? (int) ($ageConfig->total_rounds ?: 3) : (!empty($memoGames) ? count($memoGames) : 3);

            // Điểm Memo Game (mỗi lần chiến thắng = 1 điểm, tối đa $gamePlays điểm)
            $rawGameScore = (int) ($data['game_score'] ?? 0);
            $gameScore = max(0, min($rawGameScore, $gamePlays));
        }

        // Khởi tạo điểm cho các nhóm năng lực IQ
        $groupTotals = [
            'linguistic' => 0,
            'logic_math' => 0,
            'visual' => 0,
            'memory' => 0,
        ];
        foreach ($quizQuestions as $q) {
            if ($q->group) {
                $gt = is_object($q->group->type) ? $q->group->type->value : $q->group->type;
                if (isset($groupTotals[$gt])) {
                    $groupTotals[$gt]++;
                }
            }
        }

        $groupCorrects = [
            'linguistic' => 0,
            'logic_math' => 0,
            'visual' => 0,
            'memory' => 0,
        ];
        $quizQuestionIds = $quizQuestions->pluck('id')->toArray();
        $correctCount = 0;
        $processedQuestions = [];
        foreach ($answers as $answer) {
            $qId = (int) ($answer['question_id'] ?? 0);
            $ansId = (int) ($answer['answer_id'] ?? 0);

            // Bắt buộc câu hỏi phải thuộc bài trắc nghiệm hiện tại và tránh xử lý trùng lặp
            if (!$qId || !in_array($qId, $quizQuestionIds) || isset($processedQuestions[$qId])) {
                continue;
            }
            $processedQuestions[$qId] = true;

            $correct = $this->answerRepository->getByQueryBuilder([
                'id' => $ansId,
                'question_id' => $qId,
                'is_correct' => true
            ])->exists();

            if ($correct) {
                $correctCount++;
                $q = $quizQuestions->firstWhere('id', $qId);
                if ($q && $q->group) {
                    $gt = is_object($q->group->type) ? $q->group->type->value : $q->group->type;
                    if (isset($groupCorrects[$gt])) {
                        $groupCorrects[$gt]++;
                    }
                }
            }
        }

        // Memo Game được tính tương ứng $gamePlays câu thuộc nhóm Trí nhớ (Memory)
        $groupTotals['memory'] += $gamePlays;
        $groupCorrects['memory'] += $gameScore;

        foreach (['linguistic', 'logic_math', 'visual', 'memory'] as $gk) {
            $tot = $groupTotals[$gk];
            $cor = $groupCorrects[$gk];
            $data[$gk] = $tot > 0 ? "{$cor}/{$tot}" : null;
        }

        // Công thức tính điểm IQ V2:
        // Tổng số mục đánh giá = số câu hỏi trắc nghiệm + số lần chơi game ($gamePlays)
        // Tổng số đúng = correctCount (của quiz hiện tại) + gameScore
        $totalItems = $totalQuestionCount + $gamePlays;
        $totalCorrect = min($totalItems, $correctCount + $gameScore);
        $scoreValue = $totalCorrect * 10;
        $totalValue = $totalItems * 10;
        $result = "{$scoreValue}/{$totalValue}";

        $divisor = $totalItems > 0 ? ($totalItems / 10.0) : 1.5;
        $data['score'] = min(10, (int) floor($totalCorrect / $divisor));
        $data['result'] = $result;
        $data['type'] = $type;
        $data['version'] = 'v2';
        $data['game_score'] = $gameScore;
        $data['game_duration_spent'] = $data['game_duration_spent'] ?? null;
        $data['game_pairs_matched'] = $data['game_pairs_matched'] ?? null;
        $data['game_mistakes'] = $data['game_mistakes'] ?? null;
        $data['memo_theme_id'] = $data['memo_theme_id'] ?? null;
        $data['memo_age_config_id'] = $data['memo_age_config_id'] ?? null;
        $data['description'] = $this->getDescriptionByTypeAndScore($type, $data['score']);
        $data['label'] = $this->getLabelByTypeAndScore($type, $data['score']);

        $certDescription = "Đã xuất sắc nhận được kết quả đánh giá trực tuyến\nbằng cách hoàn thành Bài kiểm tra IQ nâng cao V2 của\nCHAMCON360.";
        $path = $this->createCertificate($childName, $result, $certDescription, now());
        $data['badge_image'] = $path;
        $data['status'] = VerifiedStatus::Active;

        $rating = $this->repository->update($ratingId, $data);

        // Tự động đồng bộ tạo bản ghi vào bảng memo_ratings nếu bài test IQ V2 có phần chơi game
        if ($hasGame && (!empty($data['memo_theme_id']) || $gamePlays > 0)) {
            try {
                $memoScorePercent = $gamePlays > 0 ? round(($gameScore / $gamePlays) * 100, 2) : 0;
                $label = $this->getMemoEvaluationLabel($memoScorePercent);
                $mistakes = (int) ($data['game_mistakes'] ?? 0);
                $duration = (int) ($data['game_duration_spent'] ?? 0);
                $feedback = $this->getMemoFeedback($memoScorePercent, $mistakes, $duration);

                $createdMemoRating = MemoRating::create([
                    'child_id' => $childId,
                    'memo_theme_id' => $data['memo_theme_id'] ?? (!empty($memoGames[0]['theme']['id']) ? $memoGames[0]['theme']['id'] : null),
                    'memo_age_config_id' => $data['memo_age_config_id'] ?? ($ageConfig->id ?? null),
                    'age' => (int) ($quiz->age ?? $child->age ?? 5),
                    'total_duration_spent' => $duration,
                    'total_pairs_matched' => (int) ($data['game_pairs_matched'] ?? 0),
                    'total_mistakes' => $mistakes,
                    'score' => $memoScorePercent,
                    'evaluation_label' => $label,
                    'feedback' => $feedback,
                    'status' => 'completed',
                ]);

                // Tự động ghi nhận thành tích cá nhân (Personal Best)
                $configId = $data['memo_age_config_id'] ?? ($ageConfig->id ?? null);
                if ($configId) {
                    $isWin = $gameScore > 0 && ($gameScore >= $gamePlays || $memoScorePercent >= 70);
                    $moves = (int) ($data['game_moves'] ?? (((int) ($data['game_pairs_matched'] ?? 0)) * 2 + $mistakes));
                    app(\App\Api\V1\Services\Memo\MemoPersonalBestService::class)->recordGameResult(
                        $childId,
                        $configId,
                        $duration,
                        $moves,
                        $mistakes,
                        $memoScorePercent,
                        $createdMemoRating->memo_theme_id,
                        $createdMemoRating->id,
                        $isWin
                    );
                }
            } catch (\Throwable $e) {
                Log::warning('Không thể tạo memo_ratings từ storeIQV2: ' . $e->getMessage());
            }
        }

        return $rating;
    }

    /**
     * Xếp loại kết quả Memo Game theo thang điểm 100
     */
    protected function getMemoEvaluationLabel(float $score): string
    {
        if ($score >= 85) {
            return 'Xuất sắc';
        } elseif ($score >= 70) {
            return 'Tốt';
        } elseif ($score >= 50) {
            return 'Khá';
        } else {
            return 'Cần rèn luyện';
        }
    }

    /**
     * Sinh nhận xét chuyên môn cho Memo Game
     */
    protected function getMemoFeedback(float $score, int $mistakes, int $duration): string
    {
        if ($score >= 85) {
            return "Bé có khả năng ghi nhớ thị giác và định vị không gian xuất sắc! Tốc độ nhận diện các cặp hình ảnh rất nhanh, chỉ mắc {$mistakes} lỗi trong suốt bài test. Khả năng tập trung và chuyển đổi chú ý đạt mức tối ưu.";
        } elseif ($score >= 70) {
            return "Khả năng quan sát và ghi nhớ của bé ở mức Tốt. Bé nhận diện mặt thẻ và liên kết hình ảnh khá chuẩn xác. Để tiến bộ hơn nữa, có thể tăng thử thách với các bộ thẻ nhiều chi tiết hơn.";
        } elseif ($score >= 50) {
            return "Bé hoàn thành bài test ở mức Khá. Có dấu hiệu mất tập trung nhẹ với {$mistakes} lần lật sai. Phụ huynh nên đồng hành, khích lệ bé chơi thường xuyên để nâng cao trí nhớ ngắn hạn.";
        } else {
            return "Bé cần thêm thời gian để làm quen với cấu trúc trò chơi và rèn luyện kỹ năng định vị hình ảnh. Nên bắt đầu từ mức độ Khởi động với các hình ảnh thật quen thuộc để tạo hứng thú cho bé.";
        }
    }
}
