<?php

namespace App\Api\V1\Repositories\VaccinationSchedule;

use \App\Admin\Repositories\VaccinationSchedule\VaccinationScheduleRepository as AdminModel;

class VaccinationScheduleRepository extends AdminModel implements VaccinationScheduleRepositoryInterface
{
    public function getByChilren()
    {
        return $this->model->with('child')->paginate();
    }
}
