<?php

namespace App\Api\V1\Repositories\VideoCategory;

use Illuminate\Database\Eloquent\Collection;

interface VideoCategoryRepositoryInterface
{
    /**
     * Lấy danh mục video kèm số lượng video và lọc theo nhóm tuổi nếu có
     */
    public function getCategoriesWithCounts(?int $ageGroupId = null): Collection;
}
