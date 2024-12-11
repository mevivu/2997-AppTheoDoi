<?php

namespace App\Api\V1\Services\Clinic;

use App\Api\V1\Repositories\Clinic\ClinicRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;


class ClinicService implements ClinicServiceInterface
{
    use AuthSupport, AuthServiceApi;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected ClinicRepositoryInterface $repository;


    public function __construct(
        ClinicRepositoryInterface $repository,
    ) {
        $this->repository = $repository;
    }


    public function search(array $filters)
    {
        $page = $filters['page'] ?? 1;
        $limit = $filters['limit'] ?? 10;

        return $this->repository->search($filters, $limit, $page);

    }
}
