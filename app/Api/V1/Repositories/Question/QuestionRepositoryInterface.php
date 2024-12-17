<?php

namespace App\Api\V1\Repositories\Question;


use App\Admin\Repositories\EloquentRepositoryInterface;

interface QuestionRepositoryInterface extends EloquentRepositoryInterface
{

    public function index($limit = 10, $page = 1, $question_Type = "");
}
