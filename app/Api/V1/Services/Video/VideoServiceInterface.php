<?php

namespace App\Api\V1\Services\Video;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

interface VideoServiceInterface
{
    /**
     * Kiểm tra người dùng hiện tại có gói VIP active hay không
     */
    public function isVipUser(): bool;

    /**
     * Lấy danh sách nhóm độ tuổi (hỗ trợ tính toán 3 nhóm gần nhất theo tuổi của trẻ)
     */
    public function getAgeGroups(Request $request): Collection;

    /**
     * Lấy danh mục video kèm số lượng video và lọc theo nhóm tuổi
     */
    public function getCategories(Request $request): Collection;

    /**
     * Lấy danh sách video phân trang (đã áp dụng phân quyền is_locked)
     */
    public function getVideoList(Request $request): array;

    /**
     * Lấy chi tiết video kèm danh sách video liên quan (đã áp dụng phân quyền is_locked)
     */
    public function getVideoDetail(int $id): array;

    /**
     * Tăng số lượt xem video (kèm kiểm tra VIP và chống spam qua Cache)
     */
    public function incrementView(Request $request, int $id): array;
}
