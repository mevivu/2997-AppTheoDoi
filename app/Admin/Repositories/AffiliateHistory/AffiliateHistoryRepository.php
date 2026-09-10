<?php

namespace App\Admin\Repositories\AffiliateHistory;

use App\Admin\Repositories\EloquentRepository;
use App\Models\AffiliateHistory;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repository Triển Khai Quản lý Lịch sử Hoa hồng Affiliate
 *
 * Thực thi các thao tác truy vấn và lưu trữ dữ liệu với bảng affiliate_histories
 */
class AffiliateHistoryRepository extends EloquentRepository implements AffiliateHistoryRepositoryInterface
{
    /**
     * Khai báo Model liên kết với Repository
     *
     * @return string Tên class Model
     */
    public function getModel(): string
    {
        return AffiliateHistory::class;
    }

    /**
     * Tạo mới một bản ghi lịch sử nhận thưởng hoa hồng
     *
     * @param array $data Dữ liệu cần tạo
     * @return AffiliateHistory Đối tượng vừa tạo
     */
    public function createHistory(array $data): AffiliateHistory
    {
        return $this->model->create($data);
    }

    /**
     * Lấy danh sách lịch sử nhận hoa hồng mới nhất của một người dùng
     *
     * @param int $userId ID người dùng
     * @param int $limit Số lượng bản ghi tối đa
     * @return Collection Danh sách lịch sử
     */
    public function getByUserId(int $userId, int $limit = 20): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->with(['sourceUser'])
            ->latest()
            ->take($limit)
            ->get();
    }
}
