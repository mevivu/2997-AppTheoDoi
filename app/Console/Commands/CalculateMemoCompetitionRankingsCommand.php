<?php

namespace App\Console\Commands;

use App\Enums\Memo\MemoCompetitionEntryStatus;
use App\Enums\Memo\MemoCompetitionStatus;
use App\Models\MemoCompetition;
use App\Models\MemoCompetitionEntry;
use App\Traits\UseLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;

class CalculateMemoCompetitionRankingsCommand extends Command
{
    use UseLog;

    /**
     * Tên và chữ ký của console command.
     *
     * @var string
     */
    protected $signature = 'memo:calculate-rankings {competition_id? : ID giải đấu cụ thể cần tính lại thứ hạng (tùy chọn)}';

    /**
     * Mô tả command.
     *
     * @var string
     */
    protected $description = 'Tự động tính toán và cập nhật thứ hạng cho các giải đấu Memo Game đang diễn ra và vừa kết thúc';

    /**
     * Thực thi command.
     *
     * @return int
     */
    public function handle(): int
    {
        $specificId = $this->argument('competition_id');
        $now = Carbon::now();

        try {
            if ($specificId) {
                $competitions = MemoCompetition::where('id', $specificId)->get();
                if ($competitions->isEmpty()) {
                    $this->error("Không tìm thấy giải đấu Memo Game với ID: {$specificId}");
                    return Command::FAILURE;
                }
            } else {
                // Quét tất cả các giải đang diễn ra HOẶC giải đã hết hạn nhưng chưa được chốt thứ hạng
                $competitions = MemoCompetition::where(function ($q) use ($now) {
                    $q->where('status', MemoCompetitionStatus::Active)
                        ->where('start_at', '<=', $now)
                        ->where('end_at', '>=', $now);
                })->orWhere(function ($q) use ($now) {
                    $q->where('end_at', '<', $now)
                        ->whereNull('ranking_calculated_at');
                })->get();
            }

            if ($competitions->isEmpty()) {
                $this->info("Không có giải đấu nào cần tính toán hoặc cập nhật thứ hạng lúc này.");
                return Command::SUCCESS;
            }

            $this->info("Tìm thấy {$competitions->count()} giải đấu cần cập nhật bảng xếp hạng.");

            foreach ($competitions as $competition) {
                $this->processCompetitionRankings($competition, $now);
            }

            $this->info("Hoàn tất tính toán thứ hạng giải đấu Memo Game.");
            return Command::SUCCESS;
        } catch (Throwable $e) {
            $this->error("Lỗi khi tính toán thứ hạng giải đấu: " . $e->getMessage());
            $this->logError("CalculateMemoCompetitionRankingsCommand Error: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Xử lý tính toán thứ hạng cho 1 giải đấu
     */
    protected function processCompetitionRankings(MemoCompetition $competition, Carbon $now): void
    {
        $this->line("--- Đang xử lý giải đấu #{$competition->id}: {$competition->name} ---");

        // Lấy tất cả lượt thi đã hoàn thành, sắp xếp chuẩn:
        // 1. is_valid DESC (thắng 4/4 ván lên đầu)
        // 2. total_time ASC (thời gian ngắn hơn xếp trước)
        // 3. total_moves ASC (lượt lật ít hơn xếp trước)
        // 4. completed_at ASC (hoàn thành sớm hơn xếp trước)
        $entries = MemoCompetitionEntry::where('memo_competition_id', $competition->id)
            ->where('status', MemoCompetitionEntryStatus::Completed)
            ->orderBy('is_valid', 'desc')
            ->orderBy('total_time', 'asc')
            ->orderBy('total_moves', 'asc')
            ->orderBy('completed_at', 'asc')
            ->get();

        $rankedChildren = [];
        $rank = 1;
        $totalRanked = 0;

        foreach ($entries as $entry) {
            if ($entry->is_valid) {
                // Mỗi bé chỉ nhận 1 thứ hạng tốt nhất
                if (!isset($rankedChildren[$entry->child_id])) {
                    $entry->ranking = $rank++;
                    $entry->save();
                    $rankedChildren[$entry->child_id] = true;
                    $totalRanked++;
                } else {
                    // Lượt thi phụ của bé (không được xếp hạng chính)
                    $entry->ranking = null;
                    $entry->save();
                }
            } else {
                // Không hoàn thành đủ 4 ván thắng -> không có hạng
                $entry->ranking = null;
                $entry->save();
            }
        }

        // Cập nhật mốc thời gian tính hạng gần nhất
        $competition->ranking_calculated_at = $now;

        // Nếu giải đấu đã hết hạn, chuyển trạng thái sang Đã kết thúc (Ended)
        if ($competition->end_at <= $now && $competition->status !== MemoCompetitionStatus::Ended) {
            $competition->status = MemoCompetitionStatus::Ended;
            $this->info("-> Giải đấu đã hết hạn, chuyển trạng thái sang 'Đã kết thúc'.");
        }

        $competition->save();

        $this->info("-> Đã xếp hạng cho {$totalRanked} thí sinh đủ điều kiện (tổng số lượt thi: {$entries->count()}).");
    }
}
