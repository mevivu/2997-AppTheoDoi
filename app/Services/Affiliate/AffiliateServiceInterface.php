<?php

namespace App\Services\Affiliate;

use App\Enums\User\AffiliateRank;
use App\Models\User;

/**
 * Interface Dịch Vụ Xử Lý Hoa Hồng & Cấp Bậc Affiliate
 *
 * Định nghĩa các phương thức nghiệp vụ liên quan đến chính sách hoa hồng,
 * trả thưởng đăng ký và quản lý cấp bậc mẹ giới thiệu theo doanh số.
 */
interface AffiliateServiceInterface
{
    /**
     * Xử lý kiểm tra và cộng tiền thưởng hoa hồng khi một người dùng đăng ký tài khoản có mã giới thiệu
     *
     * @param User $newUser Thực thể người dùng mới vừa được đăng ký thành công
     * @return bool Kết quả thực hiện (true: thành công / không phát sinh thưởng, false: thất bại)
     */
    public function processRegistrationReward(User $newUser): bool;

    /**
     * Ghi nhận doanh số từ giao dịch mua gói của F1 và kiểm tra nâng cấp bậc cho Người giới thiệu
     *
     * @param User $payingUser Người dùng F1 thanh toán mua gói
     * @param float $amount Số tiền thanh toán thành công
     * @return bool
     */
    public function recordSalesAndCheckRankUpgrade(User $payingUser, float $amount): bool;

    /**
     * Xác định Cấp bậc tương ứng với mức doanh số tích lũy dựa theo Cấu hình Settings
     *
     * @param float $sales Doanh số tích lũy
     * @return AffiliateRank
     */
    public function calculateRankForSales(float $sales): AffiliateRank;

    /**
     * Lấy danh sách các mốc doanh số cấu hình của 4 cấp bậc
     *
     * @return array [ 'bronze' => float, 'silver' => float, 'gold' => float, 'diamond' => float ]
     */
    public function getRankSalesThresholds(): array;
}
