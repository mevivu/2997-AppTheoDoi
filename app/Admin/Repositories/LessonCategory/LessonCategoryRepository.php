<?php

namespace App\Admin\Repositories\LessonCategory;

use App\Admin\Repositories\EloquentRepository;
use App\Models\LessonCategory;

class LessonCategoryRepository extends EloquentRepository implements LessonCategoryRepositoryInterface
{
    public function getModel()
    {
        return LessonCategory::class;
    }
}
