<?php

namespace App\Admin\Repositories\MemoCompetition;

use App\Admin\Repositories\EloquentRepository;
use App\Models\MemoCompetition;
use App\Models\MemoCompetitionEntry;
use App\Models\MemoCompetitionTheme;

class MemoCompetitionRepository extends EloquentRepository implements MemoCompetitionRepositoryInterface
{
    public function getModel(): string
    {
        return MemoCompetition::class;
    }

    /**
     * Lấy danh sách giải đấu kèm thông tin cấu hình và thống kê lượt thi
     */
    public function getListWithStats()
    {
        return $this->model->with(['ageConfig'])
            ->withCount(['entries', 'themes'])
            ->orderBy('id', 'desc')
            ->paginate(15);
    }

    /**
     * Lấy chi tiết giải đấu kèm cấu hình 4 chủ đề ván thi
     */
    public function findWithThemes(int $id)
    {
        return $this->model->with(['themes', 'competitionThemes.theme', 'ageConfig'])
            ->findOrFail($id);
    }

    /**
     * Lấy bảng xếp hạng top thành tích của giải đấu
     */
    public function getLeaderboard(int $competitionId, int $limit = 50)
    {
        return MemoCompetitionEntry::with(['child.user'])
            ->where('memo_competition_id', $competitionId)
            ->where('status', 'completed')
            ->where('is_valid', true)
            ->orderBy('total_time', 'asc')
            ->orderBy('total_moves', 'asc')
            ->orderBy('id', 'asc')
            ->take($limit)
            ->get();
    }

    /**
     * Đồng bộ danh sách 4 chủ đề và thứ tự ván thi
     */
    public function syncThemes(int $competitionId, array $themeIds): void
    {
        MemoCompetitionTheme::where('memo_competition_id', $competitionId)->delete();

        foreach ($themeIds as $index => $themeId) {
            MemoCompetitionTheme::create([
                'memo_competition_id' => $competitionId,
                'memo_theme_id' => (int) $themeId,
                'game_order' => $index + 1,
            ]);
        }
    }
}
