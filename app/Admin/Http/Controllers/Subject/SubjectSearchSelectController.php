<?php

namespace App\Admin\Http\Controllers\Subject;

use App\Admin\Http\Controllers\BaseSearchSelectController;
use App\Admin\Http\Resources\Subject\SubjectSearchSelectResource;
use App\Admin\Repositories\Subject\SubjectRepositoryInterface;

class SubjectSearchSelectController extends BaseSearchSelectController
{
    public function __construct(
        SubjectRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    protected function selectResponse(): void
    {

        $this->instance = [
            'results' => SubjectSearchSelectResource::collection($this->instance)
        ];
    }
}
