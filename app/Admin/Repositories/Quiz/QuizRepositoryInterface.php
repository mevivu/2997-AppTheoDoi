<?php

namespace App\Admin\Repositories\Quiz;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface QuizRepositoryInterface extends EloquentRepositoryInterface
{
    public function existsWithTypes(array $types): bool ;
}
