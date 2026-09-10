<?php

namespace App\Admin\Repositories\AffiliateHistory;

use App\Admin\Repositories\EloquentRepositoryInterface;
use App\Models\AffiliateHistory;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface Quản lý Repository Lịch sử Hoa hồng Affiliate
 *
 * Định nghĩa các phương thức tương tác với bảng affiliate_histories
 */
interface AffiliateHistoryRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Tạo mới một bản ghi lịch sử nhận thưởng / biến động số dư hoa hồng
     *
     * @param array $data Thông tin giao dịch hoa hồng gồm: user_id, source_user_id, amount, balance_after, type, description
     * @return AffiliateHistory Đối tượng bản ghi lịch sử vừa tạo
     */
    public function createHistory(array $data): AffiliateHistory;

    /**
     * Lấy danh sách lịch sử nhận hoa hồng của một người dùng theo phân trang hoặc giới hạn
     *
     * @param int $userId ID người dùng cần lấy lịch sử
     * @param int $limit Số lượng bản ghi cần lấy (mặc định: 20)
     * @return Collection Danh sách các bản ghi lịch sử
     */
    public function getByUserId(int $userId, int $limit = 20): Collection;
}
