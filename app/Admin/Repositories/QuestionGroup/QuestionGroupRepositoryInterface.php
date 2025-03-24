<?php

namespace App\Admin\Repositories\QuestionGroup;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface QuestionGroupRepositoryInterface extends EloquentRepositoryInterface
{
    public function getByActiveAndTypes($status, $types);
}
