<?php

namespace App\Services\Affiliate;

use App\Admin\Repositories\AffiliateHistory\AffiliateHistoryRepositoryInterface;
use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Enums\Notification\NotificationStatus;
use App\Models\Notification;
use App\Models\User;
use App\Traits\NotifiesViaFirebase;
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
     * Khởi tạo Service với các Repository phụ thuộc
     *
     * @param AffiliateHistoryRepositoryInterface $historyRepository
     * @param UserRepositoryInterface $userRepository
     * @param SettingRepositoryInterface $settingRepository
     */
    public function __construct(
        AffiliateHistoryRepositoryInterface $historyRepository,
        UserRepositoryInterface $userRepository,
        SettingRepositoryInterface $settingRepository
    ) {
        $this->historyRepository = $historyRepository;
        $this->userRepository = $userRepository;
        $this->settingRepository = $settingRepository;
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
                // 1. Thưởng hoa hồng cho Người giới thiệu (Referrer)
                if ($rewardReferrer > 0) {
                    $referrer = User::where('id', $newUser->referrer_id)->lockForUpdate()->first();
                    if ($referrer) {
                        // Cộng tiền vào ví hoa hồng của người giới thiệu
                        $referrer->wallet_balance = ($referrer->wallet_balance ?? 0) + $rewardReferrer;
                        $referrer->save();

                        // Lấy thông tin hiển thị của người đăng ký mới
                        $newUserName = $newUser->fullname ?: 'Thành viên mới';
                        $userCode = $newUser->affiliate_code ?: ($newUser->code ?: 'ID:' . $newUser->id);

                        // Lưu bản ghi lịch sử hoa hồng thông qua tầng Repository
                        $this->historyRepository->createHistory([
                            'user_id' => $referrer->id,
                            'source_user_id' => $newUser->id,
                            'amount' => $rewardReferrer,
                            'balance_after' => $referrer->wallet_balance,
                            'type' => 'referral_register',
                            'description' => "Thưởng giới thiệu thành viên {$newUserName} ({$userCode})",
                        ]);

                        // Gửi thông báo (In-app và Push FCM) cho người giới thiệu
                        $this->sendRewardNotification($referrer, $rewardReferrer, $newUserName);
                    }
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
     * Gửi thông báo trong ứng dụng (In-app) và thông báo đẩy (FCM) đến người giới thiệu
     *
     * @param User $referrer Người giới thiệu nhận hoa hồng
     * @param float $amount Số tiền hoa hồng nhận được (VNĐ)
     * @param string $newUserName Tên người dùng mới được giới thiệu
     * @return void
     */
    protected function sendRewardNotification(User $referrer, float $amount, string $newUserName): void
    {
        try {
            $formattedAmount = number_format($amount, 0, ',', '.') . 'đ';
            $title = "Bạn nhận được {$formattedAmount} hoa hồng giới thiệu!";
            $body = "Chúc mừng bạn! {$newUserName} vừa tạo tài khoản thành công qua mã giới thiệu của bạn. Số tiền {$formattedAmount} đã được cộng vào ví.";

            // 1. Tạo bản ghi thông báo trong ứng dụng (In-app notification)
            Notification::create([
                'user_id' => $referrer->id,
                'title' => $title,
                'message' => $body,
                'status' => NotificationStatus::NOT_READ,
                'type' => 'affiliate',
            ]);

            // 2. Gửi Push Notification qua Firebase nếu tài khoản có device_token
            if (!empty($referrer->device_token)) {
                $notifier = new class {
                    use NotifiesViaFirebase;
                };

                $notifier->sendFirebaseNotification(
                    [$referrer->device_token],
                    null,
                    $title,
                    $body,
                    null,
                    ['type' => 'affiliate']
                );
            }
        } catch (Throwable $e) {
            Log::warning("Không thể gửi thông báo đẩy hoa hồng affiliate: " . $e->getMessage());
        }
    }
}
