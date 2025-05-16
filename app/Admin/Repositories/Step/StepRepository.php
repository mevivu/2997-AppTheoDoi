<?php

namespace App\Admin\Repositories\Step;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Step;

class StepRepository extends EloquentRepository implements StepRepositoryInterface
{

    protected $select = [];

    public function getModel(): string
    {
        return Step::class;
    }

    public function getMaxOrder($guideId)
    {
        return $this->model->select('guide_id')
            ->selectRaw('MAX(`order`) as max_order')
            ->where('guide_id', $guideId)
            ->groupBy('guide_id')
            ->first()->max_order ?? 0;
    }
}
