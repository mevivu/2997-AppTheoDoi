<?php

namespace App\Admin\Http\Controllers\VaccinationType;

use App\Admin\Http\Controllers\BaseSearchSelectController;
use App\Admin\Http\Resources\VaccinationType\VaccinationTypeSelectResource;
use App\Admin\Repositories\VaccinationType\VaccinationTypeRepositoryInterface;

class VaccinationTypeSelectController extends BaseSearchSelectController
{
    public function __construct(
        VaccinationTypeRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }

    protected function selectResponse(): void
    {

        $this->instance = [
            'results' => VaccinationTypeSelectResource::collection($this->instance)
        ];
    }
}
