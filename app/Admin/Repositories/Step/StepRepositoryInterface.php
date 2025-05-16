<?php

namespace App\Admin\Repositories\Step;
use App\Admin\Repositories\EloquentRepositoryInterface;

interface StepRepositoryInterface extends EloquentRepositoryInterface
{
    public function getMaxOrder($guideId);
}
