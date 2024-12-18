<?php

namespace App\Api\V1\Repositories\Diaries;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Diary;


class DiariesRepository extends EloquentRepository implements DiariesRepositoryInterface
{

    public function getModel()
    {
        // TODO: Implement getModel() method.
        return Diary::class;
    }
}
