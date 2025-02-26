<?php

namespace App\Api\V1\Repositories\Bmi;

use App\Admin\Repositories\Bmi\BmiRepository as AdminArea;
use App\Enums\ActiveStatus;
class BmiRepository extends AdminArea implements BmiRepositoryInterface
{



    public function index($limit = 10, $page = 1)
    {
        // TODO: Implement index() method.
        return $this->model->where('status',ActiveStatus::Active)
            ->paginate($limit, ['*'], 'page', $page);
    }
}
