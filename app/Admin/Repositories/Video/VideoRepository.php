<?php

namespace App\Admin\Repositories\Video;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Video;

class VideoRepository extends EloquentRepository implements VideoRepositoryInterface
{
    public function getModel()
    {
        return Video::class;
    }
}
