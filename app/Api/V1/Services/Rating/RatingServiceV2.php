<?php

namespace App\Api\V1\Services\Rating;

use App\Api\V1\Services\Memo\MemoGameBuilderService;
use App\Enums\Question\QuestionType;
use App\Enums\VerifiedStatus;
use Exception;
use Illuminate\Http\Request;

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

        // Kiểm tra xem độ tuổi của bài test có cấu hình game phù hợp không
        $memoGames = MemoGameBuilderService::buildRounds((int) ($quiz->age ?? 1));
        $hasGame = !empty($memoGames);

        if (!$hasGame) {
            $gamePlays = 0;
            $gameScore = 0;
        } else {
            // Số lần chơi game lấy theo cấu hình độ tuổi (total_rounds)
            $gamePlays = count($memoGames);

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
        $correctCount = 0;
        foreach ($answers as $answer) {
            $correct = $this->answerRepository->getByQueryBuilder([
                'id' => $answer['answer_id'],
                'question_id' => $answer['question_id'],
                'is_correct' => true
            ])->exists();

            if ($correct) {
                $correctCount++;
                $q = $quizQuestions->firstWhere('id', $answer['question_id']);
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
        // Tổng số đúng = correctCount + gameScore
        $totalItems = $totalQuestionCount + $gamePlays;
        $totalCorrect = $correctCount + $gameScore;
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

        return $this->repository->update($ratingId, $data);
    }
}
