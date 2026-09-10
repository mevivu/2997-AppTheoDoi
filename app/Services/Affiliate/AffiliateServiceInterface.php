<?php

namespace App\Services\Affiliate;

use App\Models\User;

/**
 * Interface Dịch Vụ Xử Lý Hoa Hồng Affiliate
 *
 * Định nghĩa các phương thức nghiệp vụ liên quan đến chính sách hoa hồng, trả thưởng khi có người dùng đăng ký
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
}
