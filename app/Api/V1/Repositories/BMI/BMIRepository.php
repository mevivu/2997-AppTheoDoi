<?php

namespace App\Api\V1\Repositories\BMI;

use \App\Admin\Repositories\Bmi\BmiRepository as AdminArea;
use App\Models\Bmi;

class BMIRepository extends AdminArea implements BmiRepositoryInterface
{
    protected $model;

    public function __construct(Bmi $note)
    {
        $this->model = $note;
    }


    public function index($limit = 10, $page = 1)
    {
        // TODO: Implement index() method.
        return $this->model
            ->paginate($limit, ['*'], 'page', $page);
    }
}
