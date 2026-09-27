<?php

namespace App\Admin\Repositories\Lesson;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Lesson;

class LessonRepository extends EloquentRepository implements LessonRepositoryInterface
{
    public function getModel()
    {
        return Lesson::class;
    }
}
