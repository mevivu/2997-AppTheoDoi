<?php

namespace App\Admin\Repositories\Diary;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Diary;

class DiaryRepository extends EloquentRepository implements DiaryRepositoryInterface
{
    public function getModel(): string
    {
        return Diary::class;
    }
}
