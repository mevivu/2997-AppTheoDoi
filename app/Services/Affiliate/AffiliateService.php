<?php

namespace App\Services\Affiliate;

use App\Admin\Repositories\AffiliateHistory\AffiliateHistoryRepositoryInterface;
use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Api\V1\Services\Notification\NotificationServiceInterface;
use App\Enums\User\AffiliateRank;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Service Xử Lý Nghiệp Vụ Hoa Hồng Affiliate
 *
 * Chịu trách nhiệm thực hiện chính sách tính và cộng tiền thưởng hoa hồng
 * khi có thành viên mới đăng ký thông qua mã giới thiệu của người khác.
 */
class AffiliateService implements AffiliateServiceInterface
{
    /**
     * Repository thao tác với bảng lịch sử hoa hồng affiliate
     *
     * @var AffiliateHistoryRepositoryInterface
     */
    protected AffiliateHistoryRepositoryInterface $historyRepository;

    /**
     * Repository thao tác với bảng người dùng (users)
     *
     * @var UserRepositoryInterface
     */
    protected UserRepositoryInterface $userRepository;

    /**
     * Repository thao tác với bảng cấu hình hệ thống (settings)
     *
     * @var SettingRepositoryInterface
     */
    protected SettingRepositoryInterface $settingRepository;

    /**
     * Service xử lý gửi thông báo (in-app và Push notification Firebase)
     *
     * @var NotificationServiceInterface
     */
    protected NotificationServiceInterface $notificationService;

    /**
     * Khởi tạo Service với các Repository và Service phụ thuộc
     *
     * @param AffiliateHistoryRepositoryInterface $historyRepository
     * @param UserRepositoryInterface $userRepository
     * @param SettingRepositoryInterface $settingRepository
     * @param NotificationServiceInterface $notificationService
     */
    public function __construct(
        AffiliateHistoryRepositoryInterface $historyRepository,
        UserRepositoryInterface $userRepository,
        SettingRepositoryInterface $settingRepository,
        NotificationServiceInterface $notificationService
    ) {
        $this->historyRepository = $historyRepository;
        $this->userRepository = $userRepository;
        $this->settingRepository = $settingRepository;
        $this->notificationService = $notificationService;
    }

    /**
     * Xử lý cộng thưởng hoa hồng khi đăng ký tài khoản mới có mã giới thiệu
     *
     * Phương thức này chỉ được gọi trong luồng đăng ký tài khoản (khi người dùng nhập mã giới thiệu hợp lệ).
     * Quy trình xử lý:
     * 1. Kiểm tra người dùng mới có mã người giới thiệu (referrer_id) hay không.
     * 2. Kiểm tra cấu hình Admin: chương trình affiliate có đang bật hay không (affiliate_active).
     * 3. Lấy định mức tiền thưởng do Admin cấu hình (cho người giới thiệu và người mới).
     * 4. Mở Database Transaction với lockForUpdate để đảm bảo an toàn tuyệt đối số dư tài chính.
     * 5. Cộng số dư ví (wallet_balance) và lưu bản ghi lịch sử vào bảng affiliate_histories qua Repository.
     * 6. Gửi thông báo In-app và Push Notification (Firebase) cho người giới thiệu.
     *
     * @param User $newUser Thực thể người dùng mới vừa đăng ký
     * @return bool True nếu thành công, False nếu có lỗi
     */
    public function processRegistrationReward(User $newUser): bool
    {
        // Kiểm tra điều kiện tiên quyết: phải có người giới thiệu
        if (empty($newUser->referrer_id)) {
            return true;
        }

        try {
            // Đọc trạng thái chương trình Affiliate từ CSDL
            $activeSetting = $this->settingRepository->findByField('setting_key', 'affiliate_active');
            $isActive = $activeSetting ? ($activeSetting->plain_value === '1') : true;

            // Nếu Admin tắt chương trình trả thưởng thì dừng xử lý
            if (!$isActive) {
                return true;
            }

            // Lấy toàn bộ cấu hình 4 cấp bậc (Doanh số, User F1, % CK hoa hồng, Thưởng user mới)
            $thresholds = $this->getRankThresholds();

            // Lấy số tiền thưởng chào mừng cho người mới nếu Admin có cấu hình
            $rewardRefereeSetting = $this->settingRepository->findByField('setting_key', 'affiliate_reward_referee');
            $rewardReferee = $rewardRefereeSetting ? (float) $rewardRefereeSetting->plain_value : 0;

            // Thực hiện cộng tiền và ghi nhận lịch sử trong một Database Transaction an toàn
            return DB::transaction(function () use ($newUser, $thresholds, $rewardReferee) {
                // 1. Khóa và xử lý thưởng hoa hồng cho Người giới thiệu (Referrer)
                $referrer = User::where('id', $newUser->referrer_id)->lockForUpdate()->first();
                if ($referrer) {
                    $newUserName = $newUser->fullname ?: 'Thành viên mới';
                    $userCode = $newUser->affiliate_code ?: ($newUser->code ?: 'ID:' . $newUser->id);

                    // Xác định rank hiện tại của referrer để tính mức thưởng F1 mới
                    $currentRank = $referrer->affiliate_rank instanceof AffiliateRank
                        ? $referrer->affiliate_rank
                        : (AffiliateRank::tryFrom((int) $referrer->affiliate_rank) ?? AffiliateRank::Silver);

                    $rankKey = match ($currentRank) {
                        AffiliateRank::Diamond => 'diamond',
                        AffiliateRank::Platinum => 'gold',
                        AffiliateRank::Gold => 'silver',
                        default => 'bronze',
                    };

                    // Thưởng F1 mới đăng ký theo cấp bậc của Người giới thiệu:
                    // Bạc (1k), Vàng (3k), Bạch Kim (4k), Kim Cương (5k)
                    $rewardReferrer = (float) ($thresholds['rewards_user'][$rankKey] ?? 1000);

                    if ($rewardReferrer > 0) {
                        // Cộng tiền vào ví hoa hồng của người giới thiệu
                        $referrer->wallet_balance = ($referrer->wallet_balance ?? 0) + $rewardReferrer;

                        // Lưu bản ghi lịch sử hoa hồng thông qua tầng Repository
                        $this->historyRepository->createHistory([
                            'user_id' => $referrer->id,
                            'source_user_id' => $newUser->id,
                            'amount' => $rewardReferrer,
                            'balance_after' => $referrer->wallet_balance,
                            'type' => 'referral_register',
                            'description' => "Thưởng giới thiệu thành viên {$newUserName} ({$userCode}) theo cấp {$currentRank->name()}",
                        ]);
                    }

                    // Tự động kiểm tra thăng hạng theo số lượng user F1 (Điều kiện HOẶC)
                    $totalUsers = User::where('referrer_id', $referrer->id)->count();
                    $totalSales = (float) ($referrer->affiliate_total_sales ?? 0);
                    $targetRank = $this->calculateRank($totalSales, $totalUsers);

                    $isUpgraded = false;
                    if ($targetRank->value > $currentRank->value) {
                        $referrer->affiliate_rank = $targetRank;
                        $isUpgraded = true;

                        // Lưu lịch sử thăng hạng
                        $this->historyRepository->createHistory([
                            'user_id' => $referrer->id,
                            'source_user_id' => $newUser->id,
                            'amount' => 0,
                            'balance_after' => $referrer->wallet_balance ?? 0,
                            'type' => 'rank_upgrade',
                            'description' => "Thăng cấp lên {$targetRank->name()} (Số F1 đạt {$totalUsers} thành viên)",
                        ]);
                    }

                    $referrer->save();

                    // Gửi thông báo In-app & FCM thưởng giới thiệu
                    if ($rewardReferrer > 0) {
                        $this->notificationService->sendAffiliateRewardNotification($referrer, $rewardReferrer, $newUserName);
                    }

                    // Gửi thông báo chúc mừng nếu có thăng hạng
                    if ($isUpgraded) {
                        $this->notificationService->sendAffiliateRankUpgradeNotification(
                            $referrer,
                            $targetRank->name(),
                            $totalSales
                        );
                    }
                }

                // 2. Thưởng chào mừng cho Người mới đăng ký (Referee) nếu Admin cấu hình > 0
                if ($rewardReferee > 0) {
                    $freshUser = User::where('id', $newUser->id)->lockForUpdate()->first();
                    if ($freshUser) {
                        // Cộng tiền chào mừng vào ví của người mới
                        $freshUser->wallet_balance = ($freshUser->wallet_balance ?? 0) + $rewardReferee;
                        $freshUser->save();

                        // Lưu bản ghi lịch sử hoa hồng
                        $this->historyRepository->createHistory([
                            'user_id' => $freshUser->id,
                            'source_user_id' => $newUser->referrer_id,
                            'amount' => $rewardReferee,
                            'balance_after' => $freshUser->wallet_balance,
                            'type' => 'welcome_register',
                            'description' => "Thưởng chào mừng khi đăng ký qua mã giới thiệu",
                        ]);
                    }
                }

                return true;
            });
        } catch (Throwable $e) {
            // Ghi log chi tiết khi phát sinh lỗi
            Log::error("Lỗi khi xử lý thưởng hoa hồng đăng ký affiliate: " . $e->getMessage(), [
                'user_id' => $newUser->id,
                'referrer_id' => $newUser->referrer_id,
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Lấy toàn bộ cấu hình 4 cấp bậc mẹ giới thiệu từ CSDL (Doanh số, User F1, % CK hoa hồng, Thưởng user mới)
     *
     * @return array
     */
    public function getRankThresholds(): array
    {
        $keys = [
            'affiliate_sales_bronze', 'affiliate_sales_silver', 'affiliate_sales_gold', 'affiliate_sales_diamond',
            'affiliate_users_bronze', 'affiliate_users_silver', 'affiliate_users_gold', 'affiliate_users_diamond',
            'affiliate_commission_bronze', 'affiliate_commission_silver', 'affiliate_commission_gold', 'affiliate_commission_diamond',
            'affiliate_reward_user_bronze', 'affiliate_reward_user_silver', 'affiliate_reward_user_gold', 'affiliate_reward_user_diamond',
        ];

        $settings = DB::table('settings')->whereIn('setting_key', $keys)->pluck('plain_value', 'setting_key');

        return [
            'sales' => [
                'bronze' => (float) ($settings['affiliate_sales_bronze'] ?? 0),
                'silver' => (float) ($settings['affiliate_sales_silver'] ?? 10000000),
                'gold' => (float) ($settings['affiliate_sales_gold'] ?? 15000000),
                'diamond' => (float) ($settings['affiliate_sales_diamond'] ?? 20000000),
            ],
            'users' => [
                'bronze' => (int) ($settings['affiliate_users_bronze'] ?? 0),
                'silver' => (int) ($settings['affiliate_users_silver'] ?? 6000),
                'gold' => (int) ($settings['affiliate_users_gold'] ?? 8000),
                'diamond' => (int) ($settings['affiliate_users_diamond'] ?? 10000),
            ],
            'commissions' => [
                'bronze' => (float) ($settings['affiliate_commission_bronze'] ?? 10),
                'silver' => (float) ($settings['affiliate_commission_silver'] ?? 30),
                'gold' => (float) ($settings['affiliate_commission_gold'] ?? 40),
                'diamond' => (float) ($settings['affiliate_commission_diamond'] ?? 50),
            ],
            'rewards_user' => [
                'bronze' => (float) ($settings['affiliate_reward_user_bronze'] ?? 1000),
                'silver' => (float) ($settings['affiliate_reward_user_silver'] ?? 3000),
                'gold' => (float) ($settings['affiliate_reward_user_gold'] ?? 4000),
                'diamond' => (float) ($settings['affiliate_reward_user_diamond'] ?? 5000),
            ],
        ];
    }

    /**
     * Lấy danh sách các mốc doanh số cấu hình của 4 cấp bậc mẹ giới thiệu từ CSDL (VNĐ)
     *
     * @return array [ 'bronze' => float, 'silver' => float, 'gold' => float, 'diamond' => float ]
     */
    public function getRankSalesThresholds(): array
    {
        return $this->getRankThresholds()['sales'];
    }

    /**
     * Xác định Cấp bậc tương ứng theo điều kiện HOẶC:
     * Đạt mốc số lượng User F1 HOẶC đạt mốc Doanh số F1 tích lũy
     *
     * @param float $sales Doanh số F1 tích lũy (VNĐ)
     * @param int $totalUsers Tổng số user F1 đã giới thiệu
     * @return AffiliateRank
     */
    public function calculateRank(float $sales, int $totalUsers = 0): AffiliateRank
    {
        $thresholds = $this->getRankThresholds();
        $salesTh = $thresholds['sales'];
        $usersTh = $thresholds['users'];

        // Cấp 4: Kim Cương: 10.000 user HOẶC 20 triệu VNĐ
        if (($usersTh['diamond'] > 0 && $totalUsers >= $usersTh['diamond']) || ($salesTh['diamond'] > 0 && $sales >= $salesTh['diamond'])) {
            return AffiliateRank::Diamond;
        }

        // Cấp 3: Bạch Kim: 8.000 user HOẶC 15 triệu VNĐ
        if (($usersTh['gold'] > 0 && $totalUsers >= $usersTh['gold']) || ($salesTh['gold'] > 0 && $sales >= $salesTh['gold'])) {
            return AffiliateRank::Platinum;
        }

        // Cấp 2: Vàng: 6.000 user HOẶC 10 triệu VNĐ
        if (($usersTh['silver'] > 0 && $totalUsers >= $usersTh['silver']) || ($salesTh['silver'] > 0 && $sales >= $salesTh['silver'])) {
            return AffiliateRank::Gold;
        }

        // Cấp 1 mặc định: Bạc
        return AffiliateRank::Silver;
    }

    /**
     * Xác định Cấp bậc tương ứng chỉ theo mức doanh số (Hỗ trợ tương thích ngược)
     *
     * @param float $sales Doanh số F1 tích lũy
     * @return AffiliateRank
     */
    public function calculateRankForSales(float $sales): AffiliateRank
    {
        return $this->calculateRank($sales, 0);
    }

    /**
     * Ghi nhận doanh số từ giao dịch mua gói của F1, tính chiết khấu hoa hồng vào ví
     * và tự động kiểm tra nâng hạng cho Người giới thiệu
     *
     * @param User $payingUser Người dùng F1 thanh toán mua gói
     * @param float $amount Số tiền thanh toán thành công (VNĐ)
     * @param string|null $packageName Tên gói dịch vụ đã mua (tùy chọn)
     * @return bool
     */
    public function recordSalesAndCheckRankUpgrade(User $payingUser, float $amount, ?string $packageName = null): bool
    {
        if (empty($payingUser->referrer_id) || $amount <= 0) {
            return true;
        }

        try {
            return DB::transaction(function () use ($payingUser, $amount, $packageName) {
                $referrer = User::where('id', $payingUser->referrer_id)->lockForUpdate()->first();
                if (!$referrer) {
                    return true;
                }

                $thresholds = $this->getRankThresholds();

                // Cấp bậc hiện tại của Người giới thiệu
                $currentRank = $referrer->affiliate_rank instanceof AffiliateRank
                    ? $referrer->affiliate_rank
                    : (AffiliateRank::tryFrom((int) $referrer->affiliate_rank) ?? AffiliateRank::Silver);

                $rankKey = match ($currentRank) {
                    AffiliateRank::Diamond => 'diamond',
                    AffiliateRank::Platinum => 'gold',
                    AffiliateRank::Gold => 'silver',
                    default => 'bronze',
                };

                // 1. Tính và cộng hoa hồng mua gói (% CK) theo cấp bậc hiện tại của Người giới thiệu
                // Bạc: 10%, Vàng: 30%, Bạch Kim: 40%, Kim Cương: 50%
                $commissionPercent = (float) ($thresholds['commissions'][$rankKey] ?? 10);
                $commissionAmount = round($amount * ($commissionPercent / 100));

                $payingUserName = $payingUser->fullname ?: 'Thành viên F1';
                $pkgName = $packageName ?: 'Gói thành viên VIP';

                if ($commissionAmount > 0) {
                    $referrer->wallet_balance = ($referrer->wallet_balance ?? 0) + $commissionAmount;

                    $this->historyRepository->createHistory([
                        'user_id' => $referrer->id,
                        'source_user_id' => $payingUser->id,
                        'amount' => $commissionAmount,
                        'balance_after' => $referrer->wallet_balance,
                        'type' => 'package_commission',
                        'description' => "Hoa hồng {$commissionPercent}% ({$currentRank->name()}) từ đơn hàng {$pkgName} của thành viên {$payingUserName}",
                    ]);

                    // Gửi thông báo In-app và Push FCM cho Người giới thiệu
                    $this->notificationService->sendAffiliatePackageCommissionNotification(
                        $referrer,
                        $commissionAmount,
                        $commissionPercent,
                        $payingUserName,
                        $pkgName
                    );
                }

                // 2. Cộng dồn doanh số giới thiệu tích lũy
                $oldSales = (float) ($referrer->affiliate_total_sales ?? 0);
                $newSales = $oldSales + $amount;
                $referrer->affiliate_total_sales = $newSales;

                // 3. Tự động kiểm tra nâng hạng theo điều kiện HOẶC (Doanh số HOẶC Số lượng User F1)
                $totalUsers = User::where('referrer_id', $referrer->id)->count();
                $targetRank = $this->calculateRank($newSales, $totalUsers);

                // Nếu đạt cấp bậc mới cao hơn cấp hiện tại -> Thăng hạng
                $isUpgraded = false;
                if ($targetRank->value > $currentRank->value) {
                    $referrer->affiliate_rank = $targetRank;
                    $isUpgraded = true;

                    // Lưu lịch sử thăng hạng
                    $this->historyRepository->createHistory([
                        'user_id' => $referrer->id,
                        'source_user_id' => $payingUser->id,
                        'amount' => 0,
                        'balance_after' => $referrer->wallet_balance ?? 0,
                        'type' => 'rank_upgrade',
                        'description' => "Thăng cấp lên {$targetRank->name()} (Doanh số tích lũy đạt " . number_format($newSales, 0, ',', '.') . "đ, F1: {$totalUsers} thành viên)",
                    ]);
                }

                $referrer->save();

                // 4. Bắn thông báo chúc mừng thăng hạng cho người mẹ nếu có thăng cấp
                if ($isUpgraded) {
                    $this->notificationService->sendAffiliateRankUpgradeNotification(
                        $referrer,
                        $targetRank->name(),
                        $newSales
                    );
                }

                return true;
            });
        } catch (Throwable $e) {
            Log::error("Lỗi khi ghi nhận doanh số và nâng cấp bậc affiliate: " . $e->getMessage(), [
                'paying_user_id' => $payingUser->id,
                'referrer_id' => $payingUser->referrer_id,
                'amount' => $amount,
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }
}
