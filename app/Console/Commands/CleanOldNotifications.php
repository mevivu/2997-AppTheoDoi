<?php

namespace App\Console\Commands;

use App\Admin\Services\File\FileService;
use App\Enums\ApprovalStatus;
use App\Enums\Notification\MessageType;
use App\Enums\Notification\NotificationStatus;
use App\Models\Notification;
use App\Traits\UseLog;
use Exception;
use Illuminate\Console\Command;
use Throwable;

class CleanOldNotifications extends Command
{
    use UseLog;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:clean-old
                            {--days=7 : Số ngày lưu trữ trước khi xoá vĩnh viễn (mặc định: 7)}
                            {--chunk=1000 : Số lượng bản ghi xoá trong mỗi batch (mặc định: 1000)}
                            {--only-read : Chỉ xoá các thông báo đã đọc (status = 2)}
                            {--include-pending : Xoá cả các thông báo thanh toán đang chờ duyệt}
                            {--dry-run : Quét và đếm số lượng bản ghi sẽ xoá mà không thực hiện xoá thực tế}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dọn dẹp và xoá vĩnh viễn các thông báo cũ vượt quá số ngày quy định (mặc định 7 ngày)';

    protected FileService $fileService;

    public function __construct(FileService $fileService)
    {
        parent::__construct();
        $this->fileService = $fileService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $startTime = microtime(true);

        try {
            $days = max(1, (int) $this->option('days'));
            $chunkSize = max(100, (int) $this->option('chunk'));
            $onlyRead = (bool) $this->option('only-read');
            $includePending = (bool) $this->option('include-pending');
            $isDryRun = (bool) $this->option('dry-run');

            $cutoffDate = now()->subDays($days);

            // Xây dựng query lọc thông báo
            $query = Notification::where('created_at', '<=', $cutoffDate);

            // Lọc chỉ xoá thông báo đã đọc nếu có cờ --only-read
            if ($onlyRead) {
                $query->where('status', NotificationStatus::READ);
            }

            // Mặc định bảo vệ thông báo thanh toán đang chờ duyệt (trừ khi có cờ --include-pending)
            if (!$includePending) {
                $query->where(function ($q) {
                    $q->where('type', '!=', MessageType::PAYMENT->value)
                      ->orWhere('approval_status', '!=', ApprovalStatus::PENDING->value);
                });
            }

            $total = (clone $query)->count();

            $this->info("=================================================");
            $this->info("DỌN DẸP THÔNG BÁO CŨ (CRONJOB NOTIFICATION CLEANUP)");
            $this->info("=================================================");
            $this->info("Mốc thời gian xoá      : Trước {$cutoffDate->format('Y-m-d H:i:s')} (lưu trữ {$days} ngày)");
            $this->info("Chế độ chỉ xoá đã đọc  : " . ($onlyRead ? 'BẬT' : 'TẮT (xoá cả đã đọc & chưa đọc)'));
            $this->info("Bao gồm đơn chờ duyệt  : " . ($includePending ? 'BẬT' : 'TẮT (bảo vệ đơn chờ duyệt)'));
            $this->info("Kích thước mỗi batch   : {$chunkSize} bản ghi");
            $this->info("Tổng bản ghi khớp điều kiện: {$total}");

            if ($total === 0) {
                $this->info("Không có thông báo cũ nào cần dọn dẹp.");
                return Command::SUCCESS;
            }

            // Nếu đang ở chế độ Dry Run (chỉ kiểm tra thử, không xoá)
            if ($isDryRun) {
                $this->warn("[DRY RUN] Đang chạy thử nghiệm, KHÔNG có dữ liệu nào bị xoá.");
                $this->warn("[DRY RUN] Sẽ có {$total} thông báo bị xoá nếu chạy lệnh chính thức.");
                return Command::SUCCESS;
            }

            $this->info("Bắt đầu thực hiện xoá dữ liệu theo batch...");

            $deletedCount = 0;
            $deletedImagesCount = 0;

            // Xoá theo từng batch để tối ưu tài nguyên và tránh khoá bảng
            do {
                $notifications = (clone $query)
                    ->select(['id', 'payment_confirmation_image'])
                    ->limit($chunkSize)
                    ->get();

                if ($notifications->isEmpty()) {
                    break;
                }

                $ids = $notifications->pluck('id')->toArray();

                // Xoá các file ảnh xác nhận thanh toán vật lý nếu có
                foreach ($notifications as $notification) {
                    if (!empty($notification->payment_confirmation_image)) {
                        try {
                            $this->fileService->delete($notification->payment_confirmation_image);
                            $deletedImagesCount++;
                        } catch (Throwable $e) {
                            $this->logError("Lỗi khi xoá file ảnh của thông báo #{$notification->id}: " . $e->getMessage(), $e);
                        }
                    }
                }

                // Xoá vĩnh viễn các bản ghi thông báo trong batch
                $affected = Notification::whereIn('id', $ids)->delete();
                $deletedCount += $affected;

                $this->line("-> Đã xoá batch {$affected} thông báo (Tiến độ: {$deletedCount} / {$total})...");
            } while ($affected > 0);

            $elapsed = round(microtime(true) - $startTime, 2);
            $summary = "Đã dọn dẹp thành công {$deletedCount} / {$total} thông báo cũ (> {$days} ngày) và {$deletedImagesCount} file ảnh đính kèm trong {$elapsed} giây.";

            $this->info("=================================================");
            $this->info($summary);
            $this->info("=================================================");

            $this->logInfo("[notification:clean-old] {$summary}");

            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->logError("Lỗi trong quá trình dọn dẹp thông báo cũ: " . $e->getMessage(), $e);
            $this->error("Có lỗi xảy ra khi dọn dẹp thông báo: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
