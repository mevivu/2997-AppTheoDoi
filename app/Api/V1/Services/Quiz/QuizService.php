<?php

namespace App\Api\V1\Services\Quiz;


use App\Api\V1\Repositories\Quiz\QuizRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use Illuminate\Http\Request;
use App\Enums\Question\QuestionType;

class QuizService implements QuizServiceInterface
{
    use AuthSupport, AuthServiceApi;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected QuizRepositoryInterface $repository;



    public function __construct(
        QuizRepositoryInterface $repository,
    )
    {
        $this->repository = $repository;
    }

    public function getListIQ(Request $request)
    {
        $data = $request->validated();
        $age = $data['age'];
        $response = $this->repository->getByQueryBuilder(
            [
                'age' => $age,
                'type' => QuestionType::IQ
            ]
        );
        return $response->get();
    }

    public function getListAQAndEQ(Request $request)
    {
        $data = $request->validated();
        $type = $data['type'];
        $response = $this->repository->getByQueryBuilder(
            [
                'type' => $type
            ]
        );
        return $response->get();
    }
}
