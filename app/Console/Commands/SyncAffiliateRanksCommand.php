<?php

namespace App\Console\Commands;

use App\Enums\Transaction\TransactionStatus;
use App\Enums\Transaction\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Affiliate\AffiliateServiceInterface;
use App\Traits\UseLog;
use Illuminate\Console\Command;
use Throwable;

class SyncAffiliateRanksCommand extends Command
{
    use UseLog;

    /**
     * Tên và cú pháp gọi command
     *
     * @var string
     */
    protected $signature = 'affiliate:sync-ranks {--user_id= : ID người dùng cụ thể cần đồng bộ}';

    /**
     * Mô tả chức năng của command
     *
     * @var string
     */
    protected $description = 'Tính toán lại toàn bộ doanh số F1 và cập nhật cấp bậc mẹ giới thiệu (Affiliate Rank) theo cấu hình hiện tại';

    protected AffiliateServiceInterface $affiliateService;

    public function __construct(AffiliateServiceInterface $affiliateService)
    {
        parent::__construct();
        $this->affiliateService = $affiliateService;
    }

    /**
     * Thực thi lệnh
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('=== BẮT ĐẦU ĐỒNG BỘ DOANH SỐ & CẤP BẬC AFFILIATE ===');

        $userId = $this->option('user_id');
        $query = User::query();

        if ($userId) {
            $query->where('id', $userId);
        } else {
            // Lấy tất cả người dùng có người được giới thiệu hoặc đã có doanh số > 0
            $query->whereHas('referrals')->orWhere('affiliate_total_sales', '>', 0);
        }

        $totalUsers = $query->count();
        if ($totalUsers === 0) {
            $this->info('Không tìm thấy tài khoản nào có dữ liệu giới thiệu để đồng bộ.');
            return Command::SUCCESS;
        }

        $this->info("Đang xử lý đồng bộ cho {$totalUsers} tài khoản...");
        $bar = $this->output->createProgressBar($totalUsers);
        $bar->start();

        $updatedCount = 0;
        $upgradedCount = 0;

        $thresholds = $this->affiliateService->getRankThresholds();
        $salesTh = $thresholds['sales'];
        $usersTh = $thresholds['users'];
        $this->line('');
        $this->line("Ngưỡng Doanh số (VNĐ) : Đồng: " . number_format($salesTh['bronze']) . "đ | Bạc: " . number_format($salesTh['silver']) . "đ | Vàng: " . number_format($salesTh['gold']) . "đ | Kim Cương: " . number_format($salesTh['diamond']) . "đ");
        $this->line("Ngưỡng User F1 (người): Đồng: " . number_format($usersTh['bronze']) . " | Bạc: " . number_format($usersTh['silver']) . " | Vàng: " . number_format($usersTh['gold']) . " | Kim Cương: " . number_format($usersTh['diamond']));

        $query->chunk(100, function ($users) use (&$updatedCount, &$upgradedCount, $bar) {
            foreach ($users as $user) {
                try {
                    // 1. Lấy danh sách ID các F1 được user giới thiệu
                    $referralUserIds = $user->referrals()->pluck('id')->toArray();
                    $totalUsersCount = count($referralUserIds);

                    // 2. Tính tổng doanh số mua gói thành công từ các F1
                    $calculatedSales = 0;
                    if (!empty($referralUserIds)) {
                        $calculatedSales = (float) Transaction::whereIn('user_id', $referralUserIds)
                            ->where('type', TransactionType::Payment)
                            ->where('status', TransactionStatus::Confirmed)
                            ->sum('amount');
                    }

                    // 3. Xác định cấp bậc tương ứng với doanh số HOẶC số lượng user F1 này
                    $newRank = $this->affiliateService->calculateRank($calculatedSales, $totalUsersCount);
                    $oldRank = $user->affiliate_rank;

                    // 4. Cập nhật vào DB nếu có sự thay đổi
                    $hasChange = false;
                    if ((float) $user->affiliate_total_sales !== $calculatedSales) {
                        $user->affiliate_total_sales = $calculatedSales;
                        $hasChange = true;
                    }

                    if ($oldRank?->value !== $newRank->value) {
                        $user->affiliate_rank = $newRank;
                        $hasChange = true;
                        if (!$oldRank || $newRank->value > $oldRank->value) {
                            $upgradedCount++;
                        }
                    }

                    if ($hasChange) {
                        $user->save();
                        $updatedCount++;
                    }
                } catch (Throwable $e) {
                    $this->logError("Lỗi khi đồng bộ cấp bậc cho User ID {$user->id}: " . $e->getMessage(), $e);
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->line('');
        $this->info("=== HOÀN TẤT ĐỒNG BỘ: Cập nhật {$updatedCount} tài khoản ({$upgradedCount} tài khoản được nâng cấp bậc) ===");

        return Command::SUCCESS;
    }
}
