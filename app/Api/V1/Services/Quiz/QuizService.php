<?php

namespace App\Api\V1\Services\Quiz;


use App\Admin\Services\File\FileService;
use App\Api\V1\Repositories\Quiz\QuizRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use Exception;
use Illuminate\Http\Request;


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
        $type = $data['type'];
        $age = $data['age'];
        $query = $this->repository->getAllQuizzesByTypeAndAge($age, $type);
        return $query;
    }

    /**
     * @throws Exception
     */
    public function store(Request $request): object
    {
        $data = $request->validated();
        $image = $data['image'];
        if ($image) {
            $data['image'] = $this->fileService->uploadAvatar('images/pregnancy', $image);
        }
        return $this->repository->create($data);
    }

    /**
     * @throws Exception
     */
    public function update(Request $request): object
    {
        $data = $request->validated();
        $image = $data['image'];
        $pregnancy = $this->repository->findOrFail($data['id']);
        if ($image) {
            $data['image'] = $this->fileService->uploadAvatar('images/pregnancy', $image, $pregnancy->image);
        }
        $pregnancy->update($data);

        return $pregnancy;
    }


    /**
     * @throws Exception
     */
    public function delete($id): void
    {
        $response = $this->repository->findOrFail($id);
        $this->fileService->deleteModelImages($response, ['image']);
        $this->repository->delete($id);
    }
}
