<?php

namespace App\Admin\Http\Controllers\Children;

use App\Admin\Http\Controllers\BaseSearchSelectController;
use App\Admin\Http\Resources\Children\ChildrenSearchResource;
use App\Admin\Repositories\Children\UnBorn\ChildrenUnBornRepositoryInterface;


class ChildrenUnBornSelectController extends BaseSearchSelectController
{
    public function __construct(
        ChildrenUnBornRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    protected function selectResponse(): void
    {

        $this->instance = [
            'results' => ChildrenSearchResource::collection($this->instance)
        ];
    }
}
