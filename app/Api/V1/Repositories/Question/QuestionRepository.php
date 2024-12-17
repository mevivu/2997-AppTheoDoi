<?php

namespace App\Api\V1\Repositories\Question;

use \App\Admin\Repositories\Question\QuestionRepository as AdminArea;

use App\Enums\ActiveStatus;
use App\Models\Question;

class QuestionRepository extends AdminArea implements QuestionRepositoryInterface
{
    protected $model;

    public function __construct(Question $note)
    {
        $this->model = $note;
    }


    public function index($limit = 10, $page = 1, $question_Type = "")
    {
        // TODO: Implement index() method.
        return $this->model->when(!empty($question_Type), function ($query) use ($question_Type) {
            return $query->where('question_type', $question_Type);
        })->where('status', ActiveStatus::Active)->orderBy('id', 'desc')
            ->paginate($limit, ['*'], 'page', $page);
    }
}
