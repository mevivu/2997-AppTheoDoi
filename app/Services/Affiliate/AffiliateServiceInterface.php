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
     * Ghi nhận doanh số từ giao dịch mua gói của F1, tính chiết khấu hoa hồng và kiểm tra nâng cấp bậc cho Người giới thiệu
     *
     * @param User $payingUser Người dùng F1 thanh toán mua gói
     * @param float $amount Số tiền thanh toán thành công
     * @param string|null $packageName Tên gói dịch vụ đã mua (tùy chọn)
     * @return bool
     */
    public function recordSalesAndCheckRankUpgrade(User $payingUser, float $amount, ?string $packageName = null): bool;

    /**
     * Xác định Cấp bậc tương ứng với mức doanh số tích lũy và số lượng user F1 (Điều kiện HOẶC)
     *
     * @param float $sales Doanh số F1 tích lũy (VNĐ)
     * @param int $totalUsers Tổng số user F1 đã giới thiệu
     * @return AffiliateRank
     */
    public function calculateRank(float $sales, int $totalUsers = 0): AffiliateRank;

    /**
     * Xác định Cấp bậc tương ứng chỉ theo doanh số (Hỗ trợ tương thích ngược)
     *
     * @param float $sales Doanh số tích lũy
     * @return AffiliateRank
     */
    public function calculateRankForSales(float $sales): AffiliateRank;

    /**
     * Lấy toàn bộ cấu hình 4 cấp bậc (Doanh số, Số user, % Hoa hồng, Thưởng user mới)
     *
     * @return array
     */
    public function getRankThresholds(): array;

    /**
     * Lấy danh sách các mốc doanh số cấu hình của 4 cấp bậc
     *
     * @return array [ 'bronze' => float, 'silver' => float, 'gold' => float, 'diamond' => float ]
     */
    public function getRankSalesThresholds(): array;
}
