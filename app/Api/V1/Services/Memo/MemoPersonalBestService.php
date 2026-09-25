<?php

namespace App\Api\V1\Services\Memo;

use App\Models\Child;
use App\Models\MemoAgeConfig;
use App\Models\MemoPersonalBest;
use App\Models\MemoRating;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MemoPersonalBestService
{
    /**
     * Ghi nhận hoặc cập nhật thành tích cá nhân của bé khi hoàn thành ván game
     *
     * @param int $childId ID của bé
     * @param int $ageConfigId ID cấu hình level/độ tuổi (memo_age_configs)
     * @param int $duration Thời gian hoàn thành ván game (giây)
     * @param int $moves Số lượt lật bài
     * @param int $mistakes Số lần lật sai
     * @param float $score Điểm số (0 - 100)
     * @param int|null $themeId Chủ đề ván game
     * @param int|null $ratingId ID bản ghi memo_ratings tương ứng
     * @param bool $isWin Ván game có thắng (ghép hết tất cả cặp) không
     * @return MemoPersonalBest|null
     */
    public function recordGameResult(
        int $childId,
        int $ageConfigId,
        int $duration,
        int $moves,
        int $mistakes,
        float $score,
        ?int $themeId = null,
        ?int $ratingId = null,
        bool $isWin = true
    ): ?MemoPersonalBest {
        try {
            $child = Child::find($childId);
            $ageConfig = MemoAgeConfig::find($ageConfigId);

            if (!$child || !$ageConfig) {
                return null;
            }

            $personalBest = MemoPersonalBest::where('child_id', $childId)
                ->where('memo_age_config_id', $ageConfigId)
                ->first();

            $now = Carbon::now();

            if (!$personalBest) {
                // Tạo mới kỷ lục
                $personalBest = MemoPersonalBest::create([
                    'child_id' => $childId,
                    'memo_age_config_id' => $ageConfigId,
                    'best_time' => $isWin ? $duration : 0,
                    'best_moves' => $isWin ? $moves : 0,
                    'best_mistakes' => $isWin ? $mistakes : 0,
                    'best_score' => $score,
                    'memo_rating_id' => $isWin ? $ratingId : null,
                    'memo_theme_id' => $isWin ? $themeId : null,
                    'total_games_played' => 1,
                    'total_wins' => $isWin ? 1 : 0,
                    'achieved_at' => $isWin ? $now : null,
                ]);

                return $personalBest;
            }

            // Tăng số ván chơi
            $personalBest->total_games_played++;

            if ($isWin) {
                $personalBest->total_wins++;

                // Tiêu chí phá kỷ lục:
                // 1. Chưa từng thắng trước đó (best_time == 0)
                // 2. Thời gian hoàn thành mới nhanh hơn (duration < best_time)
                // 3. Thời gian bằng nhau nhưng số lần lật ít hơn (tiebreaker)
                $isNewRecord = false;
                if ($personalBest->best_time <= 0) {
                    $isNewRecord = true;
                } elseif ($duration > 0 && $duration < $personalBest->best_time) {
                    $isNewRecord = true;
                } elseif ($duration > 0 && $duration === $personalBest->best_time && $moves > 0 && $moves < $personalBest->best_moves) {
                    $isNewRecord = true;
                }

                if ($isNewRecord) {
                    $personalBest->best_time = $duration;
                    $personalBest->best_moves = $moves;
                    $personalBest->best_mistakes = $mistakes;
                    $personalBest->best_score = max($score, (float) $personalBest->best_score);
                    $personalBest->memo_rating_id = $ratingId;
                    $personalBest->memo_theme_id = $themeId;
                    $personalBest->achieved_at = $now;
                }
            }

            $personalBest->save();

            return $personalBest;
        } catch (\Throwable $e) {
            Log::error('Lỗi khi lưu Personal Best Memo Game: ' . $e->getMessage(), [
                'child_id' => $childId,
                'age_config_id' => $ageConfigId,
            ]);
            return null;
        }
    }

    /**
     * Lấy danh sách thành tích cá nhân của bé theo từng level và xác định level cao nhất
     *
     * @param int $childId
     * @return array
     */
    public function getChildPersonalBests(int $childId): array
    {
        $records = MemoPersonalBest::with(['ageConfig', 'theme'])
            ->where('child_id', $childId)
            ->get();

        // Sắp xếp theo level (rows * columns tăng dần)
        $sorted = $records->sortBy(function ($item) {
            $cards = $item->ageConfig ? ($item->ageConfig->rows * $item->ageConfig->columns) : 0;
            return $cards;
        })->values();

        // Level cao nhất có thành tích thắng
        $highestWin = $sorted->filter(function ($item) {
            return $item->total_wins > 0 && $item->best_time > 0;
        })->last();

        $formattedRecords = $sorted->map(function ($item) {
            return [
                'level_id' => $item->memo_age_config_id,
                'level_name' => $item->ageConfig?->name,
                'grid' => $item->ageConfig ? "{$item->ageConfig->rows}×{$item->ageConfig->columns}" : '--',
                'total_cards' => $item->ageConfig ? ($item->ageConfig->rows * $item->ageConfig->columns) : 0,
                'best_time' => (int) $item->best_time,
                'best_moves' => (int) $item->best_moves,
                'best_mistakes' => (int) $item->best_mistakes,
                'best_score' => (float) $item->best_score,
                'theme_name' => $item->theme?->name,
                'total_games_played' => (int) $item->total_games_played,
                'total_wins' => (int) $item->total_wins,
                'win_rate' => $item->total_games_played > 0
                    ? round(($item->total_wins / $item->total_games_played) * 100, 1)
                    : 0,
                'achieved_at' => $item->achieved_at ? Carbon::parse($item->achieved_at)->toIso8601String() : null,
            ];
        })->toArray();

        return [
            'personal_bests' => $formattedRecords,
            'highest_level' => $highestWin ? [
                'level_id' => $highestWin->memo_age_config_id,
                'level_name' => $highestWin->ageConfig?->name,
                'grid' => "{$highestWin->ageConfig->rows}×{$highestWin->ageConfig->columns}",
                'best_time' => (int) $highestWin->best_time,
                'best_moves' => (int) $highestWin->best_moves,
                'total_wins' => (int) $highestWin->total_wins,
                'achieved_at' => $highestWin->achieved_at ? Carbon::parse($highestWin->achieved_at)->toIso8601String() : null,
            ] : null,
        ];
    }
}
