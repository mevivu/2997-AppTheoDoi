<?php

namespace App\Api\V1\Services\Exercise;

use App\Api\V1\Repositories\Exercise\ExerciseRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use Illuminate\Http\Request;


class ExerciseService implements ExerciseServiceInterface
{
    use AuthSupport, AuthServiceApi;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected ExerciseRepositoryInterface $repository;


    public function __construct(
        ExerciseRepositoryInterface $repository,
    )
    {
        $this->repository = $repository;
    }


    public function index(Request $request)
    {
        // TODO: Implement index() method.
        $this->data = $request->validated();
        $page = $this->data['page'] ?? 1;
        $limit = $this->data['limit'] ?? 10;
        $type = $this->data['exercise_type'] ?? '';
        return $this->repository->index($limit, $page, $type);
    }
}
