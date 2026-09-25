<?php

namespace App\Api\V1\Services\Memo;

use App\Api\V1\Http\Resources\Memo\MemoGameDataResource;
use App\Models\Child;
use App\Models\MemoCompetition;
use App\Models\MemoCompetitionEntry;
use App\Models\MemoCompetitionRound;
use App\Models\MemoCompetitionTheme;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MemoCompetitionService
{
    /**
     * Lấy danh sách các giải đấu đang diễn ra hoặc sắp diễn ra
     */
    public function getActiveAndUpcoming(?int $childId = null): array
    {
        $now = Carbon::now();

        $competitions = MemoCompetition::with(['ageConfig', 'themes'])
            ->whereIn('status', ['active', 'upcoming', 'ended'])
            ->orderByRaw("
                CASE 
                    WHEN status = 'active' AND start_at <= '{$now}' AND end_at >= '{$now}' THEN 1
                    WHEN status = 'upcoming' OR start_at > '{$now}' THEN 2
                    ELSE 3
                END ASC
            ")
            ->orderBy('start_at', 'desc')
            ->get();

        return $competitions->map(function ($comp) use ($childId) {
            return $this->formatCompetitionSummary($comp, $childId);
        })->toArray();
    }

    /**
     * Lấy thông tin chi tiết giải đấu
     */
    public function getDetail(int $competitionId, ?int $childId = null): ?array
    {
        $comp = MemoCompetition::with(['ageConfig', 'themes', 'competitionThemes.theme'])->find($competitionId);
        if (!$comp) {
            return null;
        }

        $summary = $this->formatCompetitionSummary($comp, $childId);

        // Chi tiết danh sách 4 chủ đề
        $themesList = $comp->competitionThemes->map(function ($ct) {
            return [
                'game_order' => $ct->game_order,
                'theme_id' => $ct->memo_theme_id,
                'theme_name' => $ct->theme?->name,
                'theme_code' => $ct->theme?->code,
                'theme_icon' => $ct->theme?->icon ? asset($ct->theme->icon) : null,
            ];
        })->toArray();

        $summary['themes_detail'] = $themesList;

        // Nếu có child_id, lấy danh sách các lần thi đã thực hiện
        if ($childId) {
            $myEntries = MemoCompetitionEntry::with('rounds')
                ->where('memo_competition_id', $competitionId)
                ->where('child_id', $childId)
                ->orderBy('attempt_number', 'asc')
                ->get()
                ->map(function ($e) {
                    return [
                        'entry_id' => $e->id,
                        'attempt_number' => $e->attempt_number,
                        'status' => $e->status,
                        'is_valid' => (bool) $e->is_valid,
                        'total_time' => (int) $e->total_time,
                        'total_moves' => (int) $e->total_moves,
                        'games_won' => (int) $e->games_won,
                        'ranking' => $e->ranking,
                        'completed_at' => $e->completed_at ? Carbon::parse($e->completed_at)->toIso8601String() : null,
                    ];
                })->toArray();

            $summary['my_entries'] = $myEntries;
        }

        return $summary;
    }

    /**
     * Kiểm tra điều kiện tham gia giải đấu của bé
     */
    public function checkEligibility(MemoCompetition $comp, int $childId): array
    {
        $now = Carbon::now();

        // 1. Kiểm tra thời gian giải đấu
        if ($comp->status === 'cancelled') {
            return ['can' => false, 'reason' => 'Giải đấu đã bị hủy bỏ.'];
        }
        if ($comp->status === 'draft') {
            return ['can' => false, 'reason' => 'Giải đấu chưa được mở.'];
        }
        if ($comp->hasEnded()) {
            return ['can' => false, 'reason' => 'Giải đấu đã kết thúc.'];
        }
        if ($comp->start_at > $now) {
            return ['can' => false, 'reason' => 'Giải đấu chưa đến thời gian bắt đầu (mở vào: ' . $comp->start_at->format('d/m/Y H:i') . ').'];
        }

        // 2. Kiểm tra số lần thi
        $attemptsCount = MemoCompetitionEntry::where('memo_competition_id', $comp->id)
            ->where('child_id', $childId)
            ->count();

        // Kiểm tra xem có ván thi đang dở dang (in_progress) không
        $inProgressEntry = MemoCompetitionEntry::where('memo_competition_id', $comp->id)
            ->where('child_id', $childId)
            ->where('status', 'in_progress')
            ->first();

        if ($inProgressEntry) {
            return [
                'can' => true,
                'is_resuming' => true,
                'entry' => $inProgressEntry,
                'attempt_number' => $inProgressEntry->attempt_number,
            ];
        }

        $maxAttempts = (int) ($comp->max_attempts ?? 1);
        if ($maxAttempts > 0 && $attemptsCount >= $maxAttempts) {
            return [
                'can' => false,
                'reason' => "Bé đã hết số lượt tham gia giải đấu này (tối đa {$maxAttempts} lượt).",
                'attempts_used' => $attemptsCount,
                'max_attempts' => $maxAttempts,
            ];
        }

        return [
            'can' => true,
            'is_resuming' => false,
            'attempt_number' => $attemptsCount + 1,
            'attempts_used' => $attemptsCount,
            'max_attempts' => $maxAttempts,
        ];
    }

    /**
     * Bắt đầu một lượt thi mới trong giải đấu
     * Tạo Entry và sinh trước dữ liệu 4 ván game với peek_time = 0
     *
     * @throws Exception
     */
    public function startEntry(int $competitionId, int $childId): array
    {
        $comp = MemoCompetition::with(['ageConfig', 'competitionThemes.theme'])->find($competitionId);
        if (!$comp) {
            throw new Exception('Giải đấu không tồn tại.', 404);
        }

        $eligibility = $this->checkEligibility($comp, $childId);
        if (!$eligibility['can']) {
            throw new Exception($eligibility['reason'] ?? 'Không thể tham gia giải đấu.', 400);
        }

        DB::beginTransaction();
        try {
            $now = Carbon::now();

            if (!empty($eligibility['is_resuming']) && !empty($eligibility['entry'])) {
                $entry = $eligibility['entry'];
            } else {
                $entry = MemoCompetitionEntry::create([
                    'memo_competition_id' => $comp->id,
                    'child_id' => $childId,
                    'attempt_number' => $eligibility['attempt_number'],
                    'status' => 'in_progress',
                    'started_at' => $now,
                ]);
            }

            // Sinh dữ liệu 4 ván game theo đúng 4 chủ đề và thứ tự cấu hình
            $themes = $comp->competitionThemes->sortBy('game_order')->values();
            if ($themes->isEmpty()) {
                throw new Exception('Giải đấu chưa được cấu hình các chủ đề thi.', 400);
            }

            $gamesData = [];
            $ageConfig = clone $comp->ageConfig;
            // Ép buộc peek_time = 0 (KHÔNG cho xem trước) để chống gian lận
            $ageConfig->peek_time = 0;

            foreach ($themes as $compTheme) {
                $themeModel = $compTheme->theme;
                if (!$themeModel) {
                    continue;
                }

                $roundRaw = MemoGameBuilderService::buildSingleRound($themeModel, $ageConfig);
                if ($roundRaw) {
                    // Đảm bảo cấu hình peek_time trong payload trả về là 0
                    if (isset($roundRaw['age_config'])) {
                        $roundRaw['age_config']->peek_time = 0;
                    }
                    $resolvedRound = (new MemoGameDataResource($roundRaw))->resolve();
                    $resolvedRound['game_number'] = $compTheme->game_order;
                    $resolvedRound['theme_id'] = $themeModel->id;

                    $gamesData[] = $resolvedRound;
                }
            }

            DB::commit();

            return [
                'entry_id' => $entry->id,
                'competition_id' => $comp->id,
                'competition_name' => $comp->name,
                'attempt_number' => $entry->attempt_number,
                'total_games' => count($gamesData),
                'peek_time' => 0,
                'games' => $gamesData,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Lỗi khi bắt đầu lượt thi giải đấu: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Submit kết quả của 1 ván thi (Round) trong lượt thi
     *
     * @throws Exception
     */
    public function submitRound(int $competitionId, int $childId, array $data): array
    {
        $entryId = (int) ($data['entry_id'] ?? 0);
        $gameNumber = (int) ($data['game_number'] ?? 1);
        $duration = (int) ($data['duration_spent'] ?? 0);
        $pairsMatched = (int) ($data['pairs_matched'] ?? 0);
        $totalMoves = (int) ($data['total_moves'] ?? 0);
        $mistakes = (int) ($data['mistakes'] ?? 0);
        $isWon = (bool) ($data['is_won'] ?? false);
        $themeId = !empty($data['theme_id']) ? (int) $data['theme_id'] : null;

        $entry = MemoCompetitionEntry::where('id', $entryId)
            ->where('memo_competition_id', $competitionId)
            ->where('child_id', $childId)
            ->first();

        if (!$entry) {
            throw new Exception('Lượt thi không hợp lệ hoặc không thuộc về bé.', 404);
        }

        if ($entry->status !== 'in_progress') {
            throw new Exception('Lượt thi này đã kết thúc hoặc không còn hiệu lực.', 400);
        }

        // Lưu / cập nhật bản ghi round
        $round = MemoCompetitionRound::updateOrCreate(
            [
                'memo_competition_entry_id' => $entry->id,
                'game_number' => $gameNumber,
            ],
            [
                'memo_theme_id' => $themeId,
                'duration_spent' => $duration,
                'pairs_matched' => $pairsMatched,
                'total_moves' => $totalMoves,
                'mistakes' => $mistakes,
                'is_won' => $isWon,
                'completed_at' => Carbon::now(),
            ]
        );

        return [
            'entry_id' => $entry->id,
            'game_number' => $gameNumber,
            'saved' => true,
            'is_won' => $isWon,
        ];
    }

    /**
     * Hoàn thành lượt thi giải đấu, tổng hợp chỉ số thời gian và số lần lật
     *
     * @throws Exception
     */
    public function completeEntry(int $competitionId, int $childId, int $entryId): array
    {
        $comp = MemoCompetition::find($competitionId);
        $entry = MemoCompetitionEntry::with('rounds')
            ->where('id', $entryId)
            ->where('memo_competition_id', $competitionId)
            ->where('child_id', $childId)
            ->first();

        if (!$comp || !$entry) {
            throw new Exception('Không tìm thấy lượt thi giải đấu.', 404);
        }

        $rounds = $entry->rounds;
        $totalDuration = $rounds->sum('duration_spent');
        $totalMoves = $rounds->sum('total_moves');
        $totalMistakes = $rounds->sum('mistakes');
        $totalPairs = $rounds->sum('pairs_matched');
        $gamesWon = $rounds->where('is_won', true)->count();

        // Kiểm tra điều kiện hợp lệ: phải thắng tất cả số game quy định (mặc định 4/4)
        $requiredGames = $comp->total_games ?: 4;
        $isValid = ($gamesWon >= $requiredGames);

        $now = Carbon::now();
        $entry->update([
            'total_time' => $totalDuration,
            'total_moves' => $totalMoves,
            'total_mistakes' => $totalMistakes,
            'total_pairs_matched' => $totalPairs,
            'games_won' => $gamesWon,
            'is_valid' => $isValid,
            'status' => 'completed',
            'completed_at' => $now,
        ]);

        // Nếu giải đấu đã kết thúc, tính lại bảng xếp hạng ngay
        if ($comp->hasEnded()) {
            $this->calculateRankings($competitionId);
            $entry->refresh();
        }

        return [
            'entry_id' => $entry->id,
            'is_valid' => (bool) $entry->is_valid,
            'total_time' => (int) $entry->total_time,
            'total_moves' => (int) $entry->total_moves,
            'total_mistakes' => (int) $entry->total_mistakes,
            'games_won' => (int) $entry->games_won,
            'ranking' => $entry->ranking,
            'status' => $entry->status,
            'message' => $isValid
                ? 'Chúc mừng bé đã xuất sắc hoàn thành và chiến thắng cả 4 thử thách!'
                : "Bé đã hoàn thành lượt thi với {$gamesWon}/{$requiredGames} ván thắng. Cố gắng hơn ở lần sau nhé!",
        ];
    }

    /**
     * Tính toán và cập nhật thứ hạng của tất cả các bài thi khi giải đấu kết thúc
     *
     * Tiêu chí xếp hạng:
     * 1. Lượt thi hợp lệ (thắng 4/4 game) ưu tiên xếp trước
     * 2. Tổng thời gian hoàn thành 4 game ít hơn xếp trước
     * 3. Tiebreaker 1: Tổng số lần lật thẻ ít hơn xếp trước
     * 4. Tiebreaker 2: Hoàn thành sớm hơn (completed_at) xếp trước
     */
    public function calculateRankings(int $competitionId): void
    {
        $comp = MemoCompetition::find($competitionId);
        if (!$comp) {
            return;
        }

        // Lấy tất cả lượt thi đã hoàn thành của giải đấu
        // Nếu bé thi nhiều lần (nếu cho phép thi lại), chỉ lấy lượt thi tốt nhất của mỗi bé để xếp hạng
        $entries = MemoCompetitionEntry::where('memo_competition_id', $competitionId)
            ->where('status', 'completed')
            ->orderBy('is_valid', 'desc')
            ->orderBy('total_time', 'asc')
            ->orderBy('total_moves', 'asc')
            ->orderBy('completed_at', 'asc')
            ->get();

        // Nhóm theo child_id để lấy lượt tốt nhất của mỗi bé nếu có nhiều lần thi
        $rankedChildren = [];
        $rank = 1;

        foreach ($entries as $entry) {
            if ($entry->is_valid) {
                if (!isset($rankedChildren[$entry->child_id])) {
                    $entry->ranking = $rank++;
                    $entry->save();
                    $rankedChildren[$entry->child_id] = true;
                } else {
                    // Lượt thi phụ của bé (không được xếp hạng chính)
                    $entry->ranking = null;
                    $entry->save();
                }
            } else {
                // Không hoàn thành đủ 4 game thắng -> không có hạng chính thức
                $entry->ranking = null;
                $entry->save();
            }
        }

        $comp->ranking_calculated_at = Carbon::now();
        if ($comp->end_at <= Carbon::now() && $comp->status !== 'ended') {
            $comp->status = 'ended';
        }
        $comp->save();
    }

    /**
     * Lấy Bảng Xếp Hạng (Leaderboard) của giải đấu
     */
    public function getLeaderboard(int $competitionId, ?int $childId = null, int $limit = 50): array
    {
        $comp = MemoCompetition::with(['ageConfig'])->find($competitionId);
        if (!$comp) {
            return [
                'leaderboard' => [],
                'total_participants' => 0,
                'my_ranking' => null,
            ];
        }

        // Nếu giải đấu đã hết hạn mà chưa tính thứ hạng hoặc cần cập nhật, tính luôn
        if ($comp->hasEnded() && !$comp->ranking_calculated_at) {
            $this->calculateRankings($competitionId);
        }

        // Query bảng xếp hạng: Ưu tiên theo thứ hạng đã gán (ranking NOT NULL),
        // fallback sắp xếp theo thời gian & moves nếu chưa kết thúc giải
        $query = MemoCompetitionEntry::with('child')
            ->where('memo_competition_id', $competitionId)
            ->where('status', 'completed');

        if ($comp->ranking_calculated_at) {
            $query->whereNotNull('ranking')->orderBy('ranking', 'asc');
        } else {
            // Đang diễn ra: Bảng xếp hạng tạm thời (Provisional)
            $query->where('is_valid', true)
                ->orderBy('total_time', 'asc')
                ->orderBy('total_moves', 'asc')
                ->orderBy('completed_at', 'asc');
        }

        $allEntries = $query->get();

        // Chỉ lấy top 1 lượt thi tốt nhất của mỗi bé
        $uniqueEntries = new Collection();
        $seenChildren = [];
        $tempRank = 1;

        foreach ($allEntries as $entry) {
            if (!isset($seenChildren[$entry->child_id])) {
                $seenChildren[$entry->child_id] = true;
                $entry->display_rank = $entry->ranking ?: $tempRank++;
                $uniqueEntries->push($entry);
            }
        }

        $totalParticipants = MemoCompetitionEntry::where('memo_competition_id', $competitionId)
            ->distinct('child_id')
            ->count('child_id');

        $leaderboard = $uniqueEntries->take($limit)->map(function ($e) {
            return [
                'ranking' => $e->display_rank ?? $e->ranking,
                'child' => [
                    'id' => $e->child?->id,
                    'fullname' => $e->child?->fullname ?? 'Thí sinh',
                    'avatar' => $e->child?->avatar ? asset($e->child->avatar) : null,
                    'age' => $e->child?->age,
                ],
                'total_time' => (int) $e->total_time,
                'total_moves' => (int) $e->total_moves,
                'games_won' => (int) $e->games_won,
                'completed_at' => $e->completed_at ? Carbon::parse($e->completed_at)->toIso8601String() : null,
            ];
        })->values()->toArray();

        // Tìm thành tích của bé hiện tại
        $myRankData = null;
        if ($childId) {
            $myEntry = $uniqueEntries->firstWhere('child_id', $childId);
            if ($myEntry) {
                $myRankData = [
                    'ranking' => $myEntry->display_rank ?? $myEntry->ranking,
                    'total_time' => (int) $myEntry->total_time,
                    'total_moves' => (int) $myEntry->total_moves,
                    'games_won' => (int) $myEntry->games_won,
                    'is_valid' => (bool) $myEntry->is_valid,
                    'attempt_number' => $myEntry->attempt_number,
                ];
            }
        }

        return [
            'competition_id' => $comp->id,
            'competition_name' => $comp->name,
            'is_final' => (bool) $comp->ranking_calculated_at,
            'total_participants' => $totalParticipants,
            'leaderboard' => $leaderboard,
            'my_ranking' => $myRankData,
        ];
    }

    /**
     * Helper format thông tin tóm tắt giải đấu
     */
    protected function formatCompetitionSummary(MemoCompetition $comp, ?int $childId = null): array
    {
        $now = Carbon::now();
        $isHappening = $comp->isHappening();
        $hasEnded = $comp->hasEnded();

        $participantCount = MemoCompetitionEntry::where('memo_competition_id', $comp->id)
            ->distinct('child_id')
            ->count('child_id');

        $eligibility = $childId ? $this->checkEligibility($comp, $childId) : null;

        return [
            'id' => $comp->id,
            'name' => $comp->name,
            'slug' => $comp->slug,
            'description' => $comp->description,
            'banner_image' => $comp->banner_image ? asset($comp->banner_image) : null,
            'start_at' => $comp->start_at->toIso8601String(),
            'end_at' => $comp->end_at->toIso8601String(),
            'status' => $comp->status,
            'is_happening' => $isHappening,
            'has_ended' => $hasEnded,
            'total_games' => (int) $comp->total_games,
            'max_attempts' => (int) ($comp->max_attempts ?? 1),
            'grid' => $comp->ageConfig ? "{$comp->ageConfig->rows}×{$comp->ageConfig->columns}" : '5×6',
            'peek_time' => 0,
            'total_participants' => $participantCount,
            'can_participate' => $eligibility['can'] ?? false,
            'eligibility_reason' => $eligibility['reason'] ?? null,
            'attempts_used' => $eligibility['attempts_used'] ?? 0,
            'is_ranking_calculated' => (bool) $comp->ranking_calculated_at,
        ];
    }
}
