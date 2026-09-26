<?php

namespace App\Admin\Services\MemoCompetition;

use App\Admin\Repositories\MemoCompetition\MemoCompetitionRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Enums\Memo\MemoCompetitionEntryStatus;
use App\Enums\Memo\MemoCompetitionStatus;
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

    public function getBlockingCompetition(?int $excludeId = null): ?MemoCompetition
    {
        return MemoCompetition::query()
            ->when($excludeId, fn ($query) => $query->where('id', '!=', $excludeId))
            ->whereIn('status', [
                MemoCompetitionStatus::Draft->value,
                MemoCompetitionStatus::Upcoming->value,
                MemoCompetitionStatus::Active->value,
            ])
            ->where('end_at', '>=', Carbon::now())
            ->orderBy('start_at')
            ->first();
    }

    private function ensureCanOpenCompetition(?int $excludeId = null): void
    {
        $blockingCompetition = $this->getBlockingCompetition($excludeId);
        if (!$blockingCompetition) {
            return;
        }

        throw new \DomainException(
            "Chỉ được phép có một giải đấu chưa kết thúc. " .
            "Giải \"{$blockingCompetition->name}\" kết thúc lúc " .
            $blockingCompetition->end_at->format('d/m/Y H:i') . '.'
        );
    }

    /**
     * Tạo mới giải đấu và cấu hình 4 ván thi
     */
    public function store(Request $request)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($request, $data) {
            $this->ensureCanOpenCompetition();

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
                'rules' => $data['rules'] ?? null,
                'prizes' => $data['prizes'] ?? null,
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

        if (in_array($data['status'], [
            MemoCompetitionStatus::Draft->value,
            MemoCompetitionStatus::Upcoming->value,
            MemoCompetitionStatus::Active->value,
        ], true) && Carbon::parse($data['end_at'])->isFuture()) {
            $this->ensureCanOpenCompetition($competition->id);
        }

        return DB::transaction(function () use ($request, $data, $competition) {
            $updateData = [
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'rules' => $data['rules'] ?? null,
                'prizes' => $data['prizes'] ?? null,
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

        // Lấy tất cả lượt thi đã hoàn thành của giải đấu
        $entries = MemoCompetitionEntry::where('memo_competition_id', $competitionId)
            ->where('status', MemoCompetitionEntryStatus::Completed)
            ->orderBy('is_valid', 'desc')
            ->orderBy('total_time', 'asc')
            ->orderBy('total_moves', 'asc')
            ->orderBy('completed_at', 'asc')
            ->get();

        $rankedChildren = [];
        $rank = 1;
        $updatedCount = 0;

        foreach ($entries as $entry) {
            if ($entry->is_valid) {
                // Mỗi bé chỉ nhận 1 thứ hạng tốt nhất trên bảng xếp hạng
                if (!isset($rankedChildren[$entry->child_id])) {
                    $entry->ranking = $rank++;
                    $entry->save();
                    $rankedChildren[$entry->child_id] = true;
                    $updatedCount++;
                } else {
                    $entry->ranking = null;
                    $entry->save();
                }
            } else {
                $entry->ranking = null;
                $entry->save();
            }
        }

        $competition->update([
            'ranking_calculated_at' => Carbon::now(),
        ]);

        return $updatedCount;
    }
}
