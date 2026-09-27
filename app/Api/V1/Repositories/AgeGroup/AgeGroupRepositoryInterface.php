<?php

namespace App\Api\V1\Repositories\AgeGroup;

use App\Admin\Repositories\AgeGroup\AgeGroupRepositoryInterface as AdminRepositoryInterface;
use App\Models\AgeGroup;
use Illuminate\Database\Eloquent\Collection;

interface AgeGroupRepositoryInterface extends AdminRepositoryInterface
{
    /**
     * Lấy nhóm tuổi thai giáo (chưa sinh)
     */
    public function getPrenatalGroup(): ?AgeGroup;

    /**
     * Lấy các nhóm tuổi đầu đời (sau khi sinh)
     */
    public function getEarlyStageGroups(int $limit = 2): Collection;

    /**
     * Lấy danh sách ID nhóm tuổi gần nhất với số tháng tuổi của trẻ
     */
    public function getNearestGroupIds(int $childAgeMonths, int $limit = 3): array;

    /**
     * Lấy danh sách nhóm tuổi theo các ID và sắp xếp theo thứ tự hiển thị
     */
    public function getByIdsOrdered(array $ids): Collection;

    /**
     * Lấy tất cả các nhóm tuổi đang kích hoạt và sắp xếp chuẩn
     */
    public function getAllActiveOrdered(): Collection;
}
