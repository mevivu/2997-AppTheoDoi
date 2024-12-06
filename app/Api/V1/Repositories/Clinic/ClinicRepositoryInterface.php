<?php

namespace App\Api\V1\Repositories\Clinic;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface ClinicRepositoryInterface extends EloquentRepositoryInterface
{
    public function searchClinics(array $filters,int $limit=10,int $page=1);
}
