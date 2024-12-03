<?php

namespace App\Admin\Http\Controllers\District;

use App\Admin\Http\Controllers\BaseSearchSelectController;
use App\Admin\Repositories\District\DistrictRepositoryInterface;
use App\Admin\Http\Resources\District\DistrictSearchSelectResource;

class DistrictSearchSelectController extends BaseSearchSelectController
{
    public function __construct(
        DistrictRepositoryInterface $repository
    ){
        $this->repository = $repository;
    }

    protected function data(): void
    {
        $this->instance = $this->repository->searchAllLimit(
            $this->request->input('term', ''),
            $this->request->input('province_id', ''),
        );
    }

    protected function selectResponse(): void
    {
        $this->instance = [
            'results' => DistrictSearchSelectResource::collection($this->instance)
        ];
    }
}
