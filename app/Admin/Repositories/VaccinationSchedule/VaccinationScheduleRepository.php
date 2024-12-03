<?php

namespace App\Admin\Repositories\VaccinationSchedule;

use App\Admin\Repositories\EloquentRepository;
use App\Models\VaccinationSchedule;

class VaccinationScheduleRepository extends EloquentRepository implements VaccinationScheduleRepositoryInterface
{
    public function getModel(): string
    {
        return VaccinationSchedule::class;
    }
}
