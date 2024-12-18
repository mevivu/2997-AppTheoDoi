<?php

namespace App\Admin\Repositories\Feature;

use App\Admin\Repositories\Answer\AnswerRepositoryInterface;
use App\Admin\Repositories\EloquentRepository;
use App\Models\Feature;

class FeatureRepository extends EloquentRepository implements AnswerRepositoryInterface
{
    public function getModel(): string
    {
        return Feature::class;
    }
}
