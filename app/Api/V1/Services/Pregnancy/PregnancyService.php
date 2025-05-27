<?php

namespace App\Api\V1\Services\Pregnancy;


use App\Admin\Services\File\FileService;
use App\Api\V1\Repositories\Pregnancy\PregnancyRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use Exception;
use Illuminate\Http\Request;


class PregnancyService implements PregnancyServiceInterface
{
    use AuthSupport, AuthServiceApi;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected PregnancyRepositoryInterface $repository;

    protected FileService $fileService;


    public function __construct(
        PregnancyRepositoryInterface $repository,
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
        $query = $this->repository->getQueryBuilder();
        $query->where('child_id', $data['child_id'] ?? null);
        $query->orderBy('week', 'asc');


        return $query->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * @throws Exception
     */
    public function store(Request $request): object
    {
        $data = $request->validated();
        $image = $data['image'] ?? null;
        if ($image) {
            $data['image'] = $this->fileService->uploadAvatar('images/pregnancy', $image);
        }
        $data['week'] = $data['week'] ?? null;
        $data['weight'] = $data['weight'] ?? null;
        $data['length'] = $data['length'] ?? null;
        $data['head_circumference'] = $data['head_circumference'] ?? null;
        return $this->repository->create($data);
    }

    /**
     * @throws Exception
     */
    public function update(Request $request): object
    {
        $data = $request->validated();
        $image = $data['image'] ?? null;
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
