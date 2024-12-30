<?php

namespace App\Admin\Repositories\VaccinationType;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface VaccinationTypeRepositoryInterface extends EloquentRepositoryInterface
{
    public function searchAllLimit($value = '', $meta = [], $select = [], $limit = 12);
}
