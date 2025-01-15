<?php

namespace App\Api\V1\Services\RatingPQ;


use App\Admin\Services\File\FileService;
use App\Api\V1\Repositories\Child\ChildRepositoryInterface;
use App\Api\V1\Repositories\RatingPQ\RatingPQRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use Exception;
use Illuminate\Http\Request;



class RatingPQService implements RatingPQServiceInterface
{
    use AuthSupport, AuthServiceApi;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected RatingPQRepositoryInterface $repository;
    protected ChildRepositoryInterface $childRepository;

    protected FileService $fileService;

    public function __construct(
        RatingPQRepositoryInterface $repository,
        ChildRepositoryInterface  $childRepository,
        FileService               $fileService
    )
    {
        $this->repository = $repository;
        $this->childRepository = $childRepository;
        $this->fileService = $fileService;
    }


    public function index(Request $request)
    {
        $data = $request->validated();
        $limit = $data['limit'] ?? 10;
        $page = $data['page'] ?? 1;
        $type = $data['type'];

        $query = $this->repository->getByQueryBuilder([
            'child_id' => $data['child_id'],
            'type' => $type,
        ]);
        return $query->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * @throws Exception
     */
    public function store(Request $request): object
    {
        $data = $request->validated();
        return $this->repository->create($data);
    }



    /**
     * @throws Exception
     */
    public function delete($id): void
    {
        $response = $this->repository->findOrFail($id);
        $this->repository->delete($id);

    }


}
