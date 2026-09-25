<?php

namespace App\Admin\Services\MemoCompetition;

use Illuminate\Http\Request;

interface MemoCompetitionServiceInterface
{
    public function getBlockingCompetition(?int $excludeId = null): ?\App\Models\MemoCompetition;

    /**
     * Tạo mới giải đấu và lưu cấu hình 4 chủ đề
     */
    public function store(Request $request);

    /**
     * Cập nhật giải đấu và đồng bộ 4 chủ đề
     */
    public function update(Request $request);

    /**
     * Xóa giải đấu
     */
    public function delete($id);

    /**
     * Tính toán và chốt thứ hạng chính thức cho giải đấu
     */
    public function calculateRankings(int $competitionId): int;
}
