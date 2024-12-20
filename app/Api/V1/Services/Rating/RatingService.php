<?php

namespace App\Api\V1\Services\Rating;


use App\Admin\Services\File\FileService;
use App\Api\V1\Repositories\Rating\RatingRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use Exception;
use Illuminate\Http\Request;


class RatingService implements RatingServiceInterface
{
    use AuthSupport, AuthServiceApi;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected RatingRepositoryInterface $repository;

    protected FileService $fileService;


    public function __construct(
        RatingRepositoryInterface $repository,
        FileService                  $fileService
    )
    {
        $this->repository = $repository;
        $this->fileService = $fileService;
    }


    public function index(Request $request)
    {
        $data = $request->validated();
        $limit = $data['limit'] ?? 10;
        $page = $data['page'] ?? 1;
        $query = $this->repository->getByQueryBuilder([
            'child_id' => $data['child_id'],
        ]);
        return $query->paginate($limit, ['*'], 'page', $page);
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
