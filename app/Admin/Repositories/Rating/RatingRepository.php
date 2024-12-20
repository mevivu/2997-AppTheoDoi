<?php

namespace App\Admin\Repositories\Rating;
use App\Admin\Repositories\EloquentRepository;
use App\Models\Rating;

class RatingRepository extends EloquentRepository implements RatingRepositoryInterface
{

    protected $select = [];

    public function getModel(): string
    {
        return Rating::class;
    }

}
