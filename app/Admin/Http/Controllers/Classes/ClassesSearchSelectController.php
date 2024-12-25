<?php

namespace App\Admin\Http\Controllers\Classes;

use App\Admin\Http\Controllers\BaseSearchSelectController;
use App\Admin\Http\Resources\Classes\ClassesSearchSelectResource;
use App\Admin\Repositories\Classes\ClassesRepositoryInterface;
class ClassesSearchSelectController extends BaseSearchSelectController
{
    public function __construct(
        ClassesRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    protected function selectResponse(): void
    {

        $this->instance = [
            'results' => ClassesSearchSelectResource::collection($this->instance)
        ];
    }
}