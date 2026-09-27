<?php

namespace App\Api\V1\Repositories\VideoCategory;

use App\Admin\Repositories\VideoCategory\VideoCategoryRepositoryInterface as AdminRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface VideoCategoryRepositoryInterface extends AdminRepositoryInterface
{
    /**
     * Lấy danh mục video kèm số lượng video và lọc theo nhóm tuổi nếu có
     */
    public function getCategoriesWithCounts(?int $ageGroupId = null): Collection;
}
