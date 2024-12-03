<?php

namespace App\Admin\Http\Controllers\Ward;

use App\Admin\Http\Controllers\BaseSearchSelectController;
use App\Admin\Repositories\Ward\WardRepositoryInterface;
use App\Admin\Http\Resources\Ward\WardSearchSelectResource;

class WardSearchSelectController extends BaseSearchSelectController
{
    public function __construct(
        WardRepositoryInterface $repository
    ){
        $this->repository = $repository;
    }

    protected function data(){
        $this->instance = $this->repository->searchAllLimit(
            $this->request->input('term', ''),
            $this->request->input('district_id', ''),
        );
    }

    protected function selectResponse(): void
    {
        $this->instance = [
            'results' => WardSearchSelectResource::collection($this->instance)
        ];
    }
}
