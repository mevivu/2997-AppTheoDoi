<?php

namespace App\Api\V1\Services\ChildEvaluation;

use App\Api\V1\Repositories\ChildEvaluation\ChildEvaluationRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use Illuminate\Http\Request;


class ChildEvaluationService implements ChildEvaluationServiceInterface
{
    use AuthSupport, AuthServiceApi;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected ChildEvaluationRepositoryInterface $repository;


    public function __construct(
        ChildEvaluationRepositoryInterface $repository,
    )
    {
        $this->repository = $repository;
    }


    public function store(Request $request): object
    {
        $data = $request->validated();

        return $this->repository->create($data);

    }
}
