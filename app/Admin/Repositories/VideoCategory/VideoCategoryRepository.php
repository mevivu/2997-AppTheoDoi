<?php

namespace App\Admin\Repositories\VideoCategory;

use App\Admin\Repositories\EloquentRepository;
use App\Models\VideoCategory;

class VideoCategoryRepository extends EloquentRepository implements VideoCategoryRepositoryInterface
{
    public function getModel()
    {
        return VideoCategory::class;
    }
}
