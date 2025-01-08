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

    public function deleteMany(array $ids)
    {
        return $this->model->whereIn('id', $ids)->delete();
    }
}