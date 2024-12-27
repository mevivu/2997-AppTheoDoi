<?php

namespace App\Admin\Repositories\VaccinationType;

use App\Admin\Repositories\EloquentRepository;
use App\Models\VaccinationType;

class VaccinationTypeRepository extends EloquentRepository implements VaccinationTypeRepositoryInterface
{
    public function getModel(): string
    {
        return VaccinationType::class;
    }
}
