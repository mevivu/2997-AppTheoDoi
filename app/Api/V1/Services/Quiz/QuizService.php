<?php

namespace App\Api\V1\Services\Quiz;


use App\Admin\Services\File\FileService;
use App\Api\V1\Repositories\Quiz\QuizRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use Exception;
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

    protected FileService $fileService;


    public function __construct(
        QuizRepositoryInterface $repository,
        FileService                  $fileService
    ) {
        $this->repository = $repository;
        $this->fileService = $fileService;
    }
    public function index(Request $request)
    {
        $data = $request->validated();
        $age = $data['age'];
        $query = $this->repository->getAllQuizzesByTypeAndAge($age, QuestionType::AQ);
        return $query;
    }
}
