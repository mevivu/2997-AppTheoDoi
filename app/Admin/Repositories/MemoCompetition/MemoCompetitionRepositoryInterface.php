<?php

namespace App\Admin\Repositories\MemoCompetition;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface MemoCompetitionRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Lấy danh sách giải đấu kèm thông tin cấu hình và thống kê lượt thi
     */
    public function getListWithStats();

    /**
     * Lấy chi tiết giải đấu kèm cấu hình 4 chủ đề ván thi
     */
    public function findWithThemes(int $id);

    /**
     * Lấy bảng xếp hạng top thành tích của giải đấu
     */
    public function getLeaderboard(int $competitionId, int $limit = 50);

    /**
     * Đồng bộ danh sách 4 chủ đề và thứ tự ván thi
     */
    public function syncThemes(int $competitionId, array $themeIds): void;
}
