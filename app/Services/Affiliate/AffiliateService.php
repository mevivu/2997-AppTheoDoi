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
            // Đọc các giá trị cấu hình Affiliate từ Repository cài đặt
            $activeSetting = $this->settingRepository->findByField('setting_key', 'affiliate_active');
            $isActive = $activeSetting ? ($activeSetting->plain_value === '1') : true;

            // Nếu Admin tắt chương trình trả thưởng thì dừng xử lý
            if (!$isActive) {
                return true;
            }

            // Lấy số tiền thưởng cho người giới thiệu do Admin cấu hình
            $rewardReferrerSetting = $this->settingRepository->findByField('setting_key', 'affiliate_reward_referrer');
            $rewardReferrer = $rewardReferrerSetting ? (float) $rewardReferrerSetting->plain_value : 0;

            // Lấy số tiền thưởng chào mừng cho người mới do Admin cấu hình
            $rewardRefereeSetting = $this->settingRepository->findByField('setting_key', 'affiliate_reward_referee');
            $rewardReferee = $rewardRefereeSetting ? (float) $rewardRefereeSetting->plain_value : 0;

            // Thực hiện cộng tiền và ghi nhận lịch sử trong một Database Transaction an toàn
            return DB::transaction(function () use ($newUser, $rewardReferrer, $rewardReferee) {
                // 1. Thưởng hoa hồng và gửi thông báo cho Người giới thiệu (Referrer)
                $referrer = User::where('id', $newUser->referrer_id)->lockForUpdate()->first();
                if ($referrer) {
                    // Lấy thông tin hiển thị của người đăng ký mới
                    $newUserName = $newUser->fullname ?: 'Thành viên mới';
                    $userCode = $newUser->affiliate_code ?: ($newUser->code ?: 'ID:' . $newUser->id);

                    if ($rewardReferrer > 0) {
                        // Cộng tiền vào ví hoa hồng của người giới thiệu
                        $referrer->wallet_balance = ($referrer->wallet_balance ?? 0) + $rewardReferrer;
                        $referrer->save();

                        // Lưu bản ghi lịch sử hoa hồng thông qua tầng Repository
                        $this->historyRepository->createHistory([
                            'user_id' => $referrer->id,
                            'source_user_id' => $newUser->id,
                            'amount' => $rewardReferrer,
                            'balance_after' => $referrer->wallet_balance,
                            'type' => 'referral_register',
                            'description' => "Thưởng giới thiệu thành viên {$newUserName} ({$userCode})",
                        ]);
                    }

                    // Gửi thông báo (In-app và Push FCM) cho người giới thiệu qua NotificationService
                    $this->notificationService->sendAffiliateRewardNotification($referrer, $rewardReferrer, $newUserName);
                }

                // 2. Thưởng chào mừng cho Người mới đăng ký (Referee) nếu Admin cấu hình > 0
                if ($rewardReferee > 0) {
                    $freshUser = User::where('id', $newUser->id)->lockForUpdate()->first();
                    if ($freshUser) {
                        // Cộng tiền chào mừng vào ví của người mới
                        $freshUser->wallet_balance = ($freshUser->wallet_balance ?? 0) + $rewardReferee;
                        $freshUser->save();

                        // Lưu bản ghi lịch sử hoa hồng thông qua tầng Repository
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
            // Ghi log chi tiết khi phát sinh lỗi để phục vụ việc điều tra sự cố
            Log::error("Lỗi khi xử lý thưởng hoa hồng đăng ký affiliate: " . $e->getMessage(), [
                'user_id' => $newUser->id,
                'referrer_id' => $newUser->referrer_id,
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Lấy danh sách các mốc doanh số cấu hình của 4 cấp bậc mẹ giới thiệu từ CSDL (VNĐ)
     *
     * @return array [ 'bronze' => float, 'silver' => float, 'gold' => float, 'diamond' => float ]
     */
    public function getRankSalesThresholds(): array
    {
        $silverSetting = $this->settingRepository->findByField('setting_key', 'affiliate_sales_silver');
        $goldSetting = $this->settingRepository->findByField('setting_key', 'affiliate_sales_gold');
        $diamondSetting = $this->settingRepository->findByField('setting_key', 'affiliate_sales_diamond');
        $bronzeSetting = $this->settingRepository->findByField('setting_key', 'affiliate_sales_bronze');

        return [
            'bronze' => $bronzeSetting ? (float) $bronzeSetting->plain_value : 0,
            'silver' => $silverSetting ? (float) $silverSetting->plain_value : 2000000,
            'gold' => $goldSetting ? (float) $goldSetting->plain_value : 10000000,
            'diamond' => $diamondSetting ? (float) $diamondSetting->plain_value : 30000000,
        ];
    }

    /**
     * Xác định Cấp bậc mẹ giới thiệu tương ứng với mức doanh số tích lũy dựa theo cấu hình
     *
     * @param float $sales Doanh số F1 tích lũy
     * @return AffiliateRank
     */
    public function calculateRankForSales(float $sales): AffiliateRank
    {
        $thresholds = $this->getRankSalesThresholds();

        if ($sales >= $thresholds['diamond']) {
            return AffiliateRank::Diamond;
        }

        if ($sales >= $thresholds['gold']) {
            return AffiliateRank::Gold;
        }

        if ($sales >= $thresholds['silver']) {
            return AffiliateRank::Silver;
        }

        return AffiliateRank::Bronze;
    }

    /**
     * Ghi nhận doanh số từ giao dịch mua gói của F1 và tự động kiểm tra nâng hạng cho Người giới thiệu
     *
     * @param User $payingUser Người dùng F1 thanh toán mua gói
     * @param float $amount Số tiền thanh toán thành công (VNĐ)
     * @return bool
     */
    public function recordSalesAndCheckRankUpgrade(User $payingUser, float $amount): bool
    {
        if (empty($payingUser->referrer_id) || $amount <= 0) {
            return true;
        }

        try {
            return DB::transaction(function () use ($payingUser, $amount) {
                $referrer = User::where('id', $payingUser->referrer_id)->lockForUpdate()->first();
                if (!$referrer) {
                    return true;
                }

                // 1. Cộng dồn doanh số giới thiệu tích lũy
                $oldSales = (float) ($referrer->affiliate_total_sales ?? 0);
                $newSales = $oldSales + $amount;
                $referrer->affiliate_total_sales = $newSales;

                // 2. Tính toán cấp bậc tương ứng với doanh số mới
                $currentRank = $referrer->affiliate_rank ?? AffiliateRank::Bronze;
                $targetRank = $this->calculateRankForSales($newSales);

                // 3. Nếu đạt cấp bậc mới cao hơn cấp hiện tại -> Thăng hạng
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
                        'description' => "Thăng cấp lên {$targetRank->name()} (Doanh số tích lũy đạt " . number_format($newSales, 0, ',', '.') . "đ)",
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
