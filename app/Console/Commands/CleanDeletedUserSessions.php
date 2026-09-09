<?php

namespace App\Console\Commands;

use App\Enums\DeleteStatus;
use App\Models\UserSession;
use App\Traits\UseLog;
use Exception;
use Illuminate\Console\Command;

class CleanDeletedUserSessions extends Command
{
    use UseLog;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:clean-deleted {--days=3 : Số ngày lưu trữ trước khi xoá vĩnh viễn (0 để xoá tất cả bản ghi deleted)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dọn dẹp các access_token và phiên đăng nhập có trạng thái deleted trong bảng user_sessions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            $days = (int) $this->option('days');
            $query = UserSession::where('status', DeleteStatus::Deleted);

            if ($days > 0) {
                $cutoffDate = now()->subDays($days);
                $query->where('updated_at', '<=', $cutoffDate);
                $this->info("Bắt đầu dọn dẹp phiên đăng nhập đã xoá trước ngày {$cutoffDate->format('Y-m-d H:i:s')} (lưu trữ {$days} ngày)...");
            } else {
                $this->info("Bắt đầu dọn dẹp TOÀN BỘ phiên đăng nhập có trạng thái deleted (days=0)...");
            }

            $total = (clone $query)->count();

            if ($total === 0) {
                $this->info("Không có phiên đăng nhập đã xoá nào cần dọn dẹp.");
                return Command::SUCCESS;
            }

            $deletedCount = 0;
            // Xoá theo từng đợt 1.000 bản ghi để tối ưu hiệu năng và tránh khoá bảng
            do {
                $ids = (clone $query)->limit(1000)->pluck('id')->toArray();
                if (empty($ids)) {
                    break;
                }
                $affected = UserSession::whereIn('id', $ids)->delete();
                $deletedCount += $affected;
            } while ($affected > 0);

            $this->logInfo("Cleaned {$deletedCount} deleted user sessions (retention days: {$days}).");
            $this->info("Đã dọn dẹp thành công {$deletedCount} / {$total} phiên đăng nhập đã xoá.");

            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->logError("Failed to clean deleted user sessions:", $e);
            $this->error("Có lỗi xảy ra khi dọn dẹp phiên đăng nhập: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
