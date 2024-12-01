<?php

namespace App\Admin\Repositories\Bmi;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Bmi;

class BmiRepository extends EloquentRepository implements BmiRepositoryInterface
{
    public function getModel()
    {
        return Bmi::class;
    }
}