<?php

namespace App\Api\V1\Services\Assessment;

use App\Api\V1\Repositories\Assessment\AssessmentRepositoryInterface;

use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use Illuminate\Http\Request;


class AssessmentService implements AssessmentServiceInterface
{
    use AuthSupport, AuthServiceApi;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected AssessmentRepositoryInterface $repository;


    public function __construct(
        AssessmentRepositoryInterface $repository,
    )
    {
        $this->repository = $repository;
    }


    public function index(Request $request)
    {
        $data = $request->validated();
        return $this->repository->getBy([
            'child_id' => $data['child_id'],
        ]);
    }
}
