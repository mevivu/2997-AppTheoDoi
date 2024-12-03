<?php

namespace App\Admin\Repositories\Question;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Question;

class QuestionRepository extends EloquentRepository implements QuestionRepositoryInterface
{
    public function getModel()
    {
        return Question::class;
    }
}
