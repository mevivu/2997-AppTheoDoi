<?php

namespace App\Admin\Http\Controllers\ClinicType;

use App\Admin\Http\Controllers\BaseSearchSelectController;
use App\Admin\Http\Resources\ClinicType\ClinicTypeSearchSelectResource;
use App\Admin\Repositories\ClinicType\ClinicTypeRepositoryInterface;

class ClinicTypeSearchSelectController extends BaseSearchSelectController
{
    public function __construct(
        ClinicTypeRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    protected function selectResponse(): void
    {
        $this->instance = [
            'results' => ClinicTypeSearchSelectResource::collection($this->instance)
        ];
    }
}
