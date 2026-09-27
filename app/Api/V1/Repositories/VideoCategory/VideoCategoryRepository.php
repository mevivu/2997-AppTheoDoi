<?php

namespace App\Api\V1\Repositories\VideoCategory;

use Illuminate\Database\Eloquent\Collection;

class VideoCategoryRepository implements VideoCategoryRepositoryInterface
{
    public function getCategoriesWithCounts(?int $ageGroupId = null): Collection
    {
        return new Collection();
    }
}
