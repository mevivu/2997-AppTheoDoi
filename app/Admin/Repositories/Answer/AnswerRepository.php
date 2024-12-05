<?php

namespace App\Admin\Repositories\Answer;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Answer;

class AnswerRepository extends EloquentRepository implements AnswerRepositoryInterface
{
    public function getModel()
    {
        return Answer::class;
    }
}