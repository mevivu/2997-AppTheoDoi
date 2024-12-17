<?php

namespace App\Api\V1\Services\Child;

use App\Api\V1\Repositories\Child\ChildRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use Illuminate\Http\Client\Request;


class ChildService implements ChildServiceInterface
{
    use AuthSupport, AuthServiceApi;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected ChildRepositoryInterface $repository;


    public function __construct(
        ChildRepositoryInterface $repository,
    ) {
        $this->repository = $repository;
    }


    public function store(Request $request)
    {
        $data = $request->validated();
    }
}
