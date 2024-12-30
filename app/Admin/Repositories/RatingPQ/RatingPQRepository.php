<?php

namespace App\Admin\Repositories\RatingPQ;
use App\Admin\Repositories\EloquentRepository;
use App\Models\RatingPQ;

class RatingPQRepository extends EloquentRepository implements RatingPQRepositoryInterface
{

    protected $select = [];

    public function getModel(): string
    {
        return RatingPQ::class;
    }

}
