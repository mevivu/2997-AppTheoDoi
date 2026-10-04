<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\ParentRank\ParentRankServiceInterface;
use App\Traits\UseLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;

class CalculateParentRanksCommand extends Command
{
    use UseLog;

    /**
     * Tên và chữ ký của console command.
     *
     * @var string
     */
    protected $signature = 'parent-rank:calculate
                            {--period= : Kỳ đánh giá theo định dạng YYYY-MM (mặc định: tháng hiện tại, truyền "prev" để tính tháng trước)}
                            {--user_id= : ID phụ huynh cụ thể cần tính lại thứ hạng}
                            {--finalize : Đánh dấu đã chốt sổ kỳ này (is_final = true)}
                            {--dry-run : Chạy thử và in kết quả ra màn hình mà không lưu vào DB}';

    /**
     * Mô tả command.
     *
     * @var string
     */
    protected $description = 'Tự động tính toán điểm số và cập nhật cấp bậc phân hạng Bố mẹ (Parent Rank) theo kỳ tháng';

    protected ParentRankServiceInterface $parentRankService;

    public function __construct(ParentRankServiceInterface $parentRankService)
    {
        parent::__construct();
        $this->parentRankService = $parentRankService;
    }

    /**
     * Thực thi command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('=== BẮT ĐẦU TÍNH TOÁN PHÂN HẠNG BỐ MẸ ===');

        // Xác định kỳ tính toán (YYYY-MM)
        $periodOption = $this->option('period');
        $now = Carbon::now('Asia/Ho_Chi_Minh');

        if ($periodOption === 'prev') {
            $period = $now->copy()->subMonth()->format('Y-m');
        } elseif (!empty($periodOption)) {
            $period = $periodOption;
        } else {
            $period = $now->format('Y-m');
        }

        $userId = $this->option('user_id');
        $finalize = (bool) $this->option('finalize');
        $dryRun = (bool) $this->option('dry-run');

        $this->line("Kỳ đánh giá: <fg=yellow>{$period}</> | Chốt sổ: " . ($finalize ? '<fg=red>CÓ (Finalize)</>' : 'Chưa') . ($dryRun ? ' | Chế độ: <fg=cyan>DRY-RUN (Thử nghiệm)</>' : ''));

        $config = $this->parentRankService->getConfig();
        if (!$config['active']) {
            $this->warn('Hệ thống Phân hạng Bố mẹ hiện đang bị TẮT trong cấu hình CSDL.');
            if (!$dryRun && !$this->confirm('Bạn có muốn tiếp tục chạy không?')) {
                return Command::SUCCESS;
            }
        }

        // Nếu chỉ tính cho 1 user
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("Không tìm thấy người dùng có ID: {$userId}");
                return Command::FAILURE;
            }

            $metrics = $this->parentRankService->collectMetrics($user, $period);
            $scores = $this->parentRankService->computeScores($metrics, $config);

            $this->table(
                ['Chỉ số', 'Giá trị thô', 'Điểm thành phần (0-100)', 'Trọng số (%)'],
                [
                    ['Thời gian dùng app', "{$metrics['usage_minutes']} phút", $scores['score_usage'], "{$config['weights']['usage']}%"],
                    ['Tần suất vào app', "{$metrics['active_days']} ngày ({$metrics['session_count']} phiên)", $scores['score_frequency'], "{$config['weights']['frequency']}%"],
                    ['Bài đánh giá cho con', "{$metrics['assessment_count']} bài", $scores['score_assessment'], "{$config['weights']['assessment']}%"],
                    ['Chỉ số con trung bình', "{$metrics['child_score_avg']} điểm", $scores['score_child'], "{$config['weights']['child_score']}%"],
                ]
            );

            $this->info("Tổng điểm: {$scores['total_points']} / 100 | Hạng đạt được: {$scores['rank']->name()} (Cấp {$scores['rank']->value})");

            if (!$dryRun) {
                $this->parentRankService->calculateAndSave($user, $period, $finalize);
                $this->info("Đã cập nhật dữ liệu thành công cho User ID {$user->id}.");
            }

            return Command::SUCCESS;
        }

        // Tính cho tất cả người dùng
        $query = User::query();
        $totalUsers = $query->count();

        if ($totalUsers === 0) {
            $this->info('Không tìm thấy người dùng nào trong hệ thống.');
            return Command::SUCCESS;
        }

        $this->info("Đang xử lý phân hạng cho {$totalUsers} tài khoản...");
        $bar = $this->output->createProgressBar($totalUsers);
        $bar->start();

        if ($dryRun) {
            $processedCount = 0;
            $rankCounts = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0];

            $query->chunk(200, function ($users) use ($period, $config, &$processedCount, &$rankCounts, $bar) {
                foreach ($users as $user) {
                    try {
                        $metrics = $this->parentRankService->collectMetrics($user, $period);
                        $scores = $this->parentRankService->computeScores($metrics, $config);
                        $rankVal = $scores['rank']->value;
                        $rankCounts[$rankVal] = ($rankCounts[$rankVal] ?? 0) + 1;
                        $processedCount++;
                    } catch (Throwable $e) {
                        $this->logError("Lỗi dry-run tính phân hạng User {$user->id}: " . $e->getMessage(), $e);
                    }
                    $bar->advance();
                }
            });

            $bar->finish();
            $this->line('');
            $this->info("=== HOÀN TẤT DRY-RUN ({$processedCount} tài khoản) ===");
            $this->table(
                ['Hạng', 'Số lượng tài khoản'],
                [
                    ['Thành viên mới (<20đ)', $rankCounts[0] ?? 0],
                    ['Đồng (≥20đ)', $rankCounts[1] ?? 0],
                    ['Bạc (≥40đ)', $rankCounts[2] ?? 0],
                    ['Vàng (≥60đ)', $rankCounts[3] ?? 0],
                    ['Kim Cương (≥80đ)', $rankCounts[4] ?? 0],
                ]
            );

            return Command::SUCCESS;
        }

        $processedCount = $this->parentRankService->calculateAll($period, $finalize, function ($user, $count) use ($bar) {
            $bar->advance();
        });

        $bar->finish();
        $this->line('');
        $this->info("=== HOÀN TẤT: Đã tính toán và cập nhật thứ hạng cho {$processedCount} tài khoản trong kỳ {$period} ===");

        return Command::SUCCESS;
    }
}
