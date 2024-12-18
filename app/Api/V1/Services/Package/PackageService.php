<?php

namespace App\Api\V1\Services\Package;

use App\Api\V1\Repositories\Package\PackageRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use App\Enums\Package\PackageStatus;
use Illuminate\Http\Request;


class PackageService implements PackageServiceInterface
{
    use AuthSupport, AuthServiceApi;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected PackageRepositoryInterface $repository;


    public function __construct(
        PackageRepositoryInterface $repository,
    )
    {
        $this->repository = $repository;
    }


    public function index()
    {
        return $this->repository->getBy([
            'status' => PackageStatus::Active,
        ]);
    }
}
