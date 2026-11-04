<?php

namespace App\Console\Commands;

use App\Admin\Repositories\UserPackage\UserPackageRepositoryInterface;
use App\Admin\Repositories\UserSession\UserSessionRepositoryInterface;
use App\Enums\Package\PackageType;
use App\Models\Package;
use App\Traits\UseLog;
use Exception;
use Illuminate\Console\Command;

class UpdateExpiredPackages extends Command
{
    use UseLog;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:update-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates expired packages to a normal package type';

    protected UserPackageRepositoryInterface $userPackageRepository;
    protected UserSessionRepositoryInterface $userSessionRepository;


    public function __construct(
        UserPackageRepositoryInterface $userPackageRepository,
        UserSessionRepositoryInterface $userSessionRepository,
    )
    {
        parent::__construct();
        $this->userPackageRepository = $userPackageRepository;
        $this->userSessionRepository = $userSessionRepository;

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            // 1. Đối soát tự động tất cả user có giao dịch thanh toán thành công (Confirmed) gần đây
            // nhưng user_packages đang bị để ở trạng thái normal hoặc end_date bị sai
            $recentConfirmedUserIds = \App\Models\Transaction::query()
                ->where('status', \App\Enums\Transaction\TransactionStatus::Confirmed)
                ->where('type', \App\Enums\Transaction\TransactionType::Payment)
                ->where('is_deleted', \App\Enums\DeleteStatus::NotDeleted)
                ->where('created_at', '>=', now()->subYears(2))
                ->distinct()
                ->pluck('user_id');

            if ($recentConfirmedUserIds->isNotEmpty()) {
                $usersToReconcile = \App\Models\User::whereIn('id', $recentConfirmedUserIds)->get();
                foreach ($usersToReconcile as $user) {
                    $user->reconcileActivePackage();
                }
            }

            // 2. Chỉ hạ cấp những gói đã thực sự hết hạn sau khi đã đối soát giao dịch
            $query = $this->userPackageRepository->getByQueryBuilder([['current_type', '!=', PackageType::Normal]]);
            $expiredPackages = $query->where('end_date', '<=', now())->get();
            $packageNormal = Package::getNormalPackage();
            foreach ($expiredPackages as $userPackage) {
                if ($userPackage->user) {
                    $reconciled = $userPackage->user->reconcileActivePackage();
                    if ($reconciled && \Carbon\Carbon::parse($reconciled->end_date)->gt(now())) {
                        continue;
                    }
                }

                $result = $userPackage->update([
                    'current_type' => PackageType::Normal,
                    'package_id' => $packageNormal->id,
                ]);
                if ($result) {
                    $this->logInfo("Updated package {$userPackage->id} successfully.");
                    // Invalidate all active sessions for the user to restrict to a single device
                    $this->userSessionRepository->deleteAllSessionTokens($userPackage->user_id);
                } else {
                    $this->logInfo("Error: Failed to update package {$userPackage->id}.");
                }
            }
            $this->logInfo('Update packages Normal successfully');
        } catch (Exception $e) {
            $this->error('Có lỗi xảy ra: ' . $e->getMessage());
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }
}
