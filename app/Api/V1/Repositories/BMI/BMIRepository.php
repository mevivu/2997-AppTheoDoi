<?php

namespace App\Api\V1\Repositories\BMI;

use App\Admin\Repositories\Bmi\BmiRepository as AdminArea;
use App\Enums\ActiveStatus;
use App\Models\Bmi;
use App\Api\V1\Repositories\BMI\BmiRepositoryInterface;
class BMIRepository extends AdminArea implements BMIRepositoryInterface
{
    protected $model;

    public function __construct(Bmi $note)
    {
        $this->model = $note;
    }


    public function index($limit = 10, $page = 1)
    {
        // TODO: Implement index() method.
        return $this->model->where('status',ActiveStatus::Active)
            ->paginate($limit, ['*'], 'page', $page);
    }
}
