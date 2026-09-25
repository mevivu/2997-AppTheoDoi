<?php

namespace App\Admin\Services\MemoCompetition;

use App\Admin\Repositories\MemoCompetition\MemoCompetitionRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Models\MemoCompetition;
use App\Models\MemoCompetitionEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MemoCompetitionService implements MemoCompetitionServiceInterface
{
    protected MemoCompetitionRepositoryInterface $repository;
    protected FileService $fileService;

    public function __construct(
        MemoCompetitionRepositoryInterface $repository,
        FileService $fileService
    ) {
        $this->repository = $repository;
        $this->fileService = $fileService;
    }

    /**
     * Tạo mới giải đấu và cấu hình 4 ván thi
     */
    public function store(Request $request)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($request, $data) {
            $bannerPath = null;
            if ($request->hasFile('banner_image')) {
                $bannerPath = $this->fileService->setFolder('images/memo/competitions')
                    ->setFile($request->file('banner_image'))
                    ->upload()
                    ->getInstance();
            }

            $competition = $this->repository->create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']) . '-' . time(),
                'description' => $data['description'] ?? null,
                'banner_image' => $bannerPath,
                'start_at' => $data['start_at'],
                'end_at' => $data['end_at'],
                'memo_age_config_id' => $data['memo_age_config_id'],
                'total_games' => 4,
                'peek_time_override' => 0,
                'max_attempts' => isset($data['max_attempts']) ? (int) $data['max_attempts'] : 1,
                'must_win_all' => true,
                'status' => $data['status'],
                'created_by' => auth('admin')->id(),
            ]);

            // Lưu 4 chủ đề theo đúng thứ tự 1 -> 4
            $this->repository->syncThemes($competition->id, $data['theme_ids']);

            return $competition;
        });
    }

    /**
     * Cập nhật thông tin giải đấu và 4 chủ đề
     */
    public function update(Request $request)
    {
        $data = $request->validated();
        $competition = $this->repository->findOrFail($data['id']);

        return DB::transaction(function () use ($request, $data, $competition) {
            $updateData = [
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'start_at' => $data['start_at'],
                'end_at' => $data['end_at'],
                'memo_age_config_id' => $data['memo_age_config_id'],
                'max_attempts' => isset($data['max_attempts']) ? (int) $data['max_attempts'] : 1,
                'status' => $data['status'],
            ];

            // Cập nhật banner nếu người dùng upload file mới
            if ($request->hasFile('banner_image')) {
                if ($competition->banner_image) {
                    $this->fileService->delete($competition->banner_image);
                }
                $updateData['banner_image'] = $this->fileService->setFolder('images/memo/competitions')
                    ->setFile($request->file('banner_image'))
                    ->upload()
                    ->getInstance();
            }

            $this->repository->update($competition->id, $updateData);

            // Đồng bộ lại 4 chủ đề
            $this->repository->syncThemes($competition->id, $data['theme_ids']);

            return $competition->fresh();
        });
    }

    /**
     * Xóa giải đấu và các dữ liệu liên quan
     */
    public function delete($id)
    {
        $competition = $this->repository->findOrFail($id);

        if ($competition->banner_image) {
            $this->fileService->delete($competition->banner_image);
        }

        return $this->repository->delete($id);
    }

    /**
     * Tính toán và chốt thứ hạng chính thức cho giải đấu
     */
    public function calculateRankings(int $competitionId): int
    {
        $competition = $this->repository->findOrFail($competitionId);

        // Lấy tất cả lượt thi hợp lệ (hoàn thành xuất sắc cả 4 ván)
        $entries = MemoCompetitionEntry::where('memo_competition_id', $competitionId)
            ->where('status', 'completed')
            ->where('is_valid', true)
            ->orderBy('total_time', 'asc')
            ->orderBy('total_moves', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $rank = 1;
        $updatedCount = 0;
        foreach ($entries as $entry) {
            $entry->update(['ranking' => $rank]);
            $rank++;
            $updatedCount++;
        }

        $competition->update([
            'ranking_calculated_at' => Carbon::now(),
        ]);

        return $updatedCount;
    }
}
