<?php

namespace App\Api\V1\Services\Journal;


use App\Admin\Services\File\FileService;
use App\Api\V1\Repositories\Journal\JournalRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class JournalService implements JournalServiceInterface
{
    use AuthSupport, AuthServiceApi;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected JournalRepositoryInterface $repository;

    protected FileService $fileService;


    public function __construct(
        JournalRepositoryInterface $repository,
        FileService $fileService
    )
    {
        $this->repository = $repository;
        $this->fileService = $fileService;
    }


    public function index(Request $request)
    {
        $data = $request->validated();
        $type = $data['type'];
        $limit = $data['limit'] ?? 10;
        $page = $data['page'] ?? 1;
        $query = $this->repository->getByQueryBuilder([
            'type' => $type,
            'child_id' => $data['child_id'],
        ]);
        return $query->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * @throws \Exception
     */
    public function store(Request $request): object
    {
        $data = $request->validated();
        $data['image'] = $this->uploadPhotos($request->file('image') ?? []);
        return $this->repository->create($data);
    }

    protected function uploadPhotos($photos): string
    {
        $paths = [];
        foreach ($photos as $photo) {
            if ($photo->isValid()) {
                $uploadedPath = $this->fileService->uploadAvatar('images/journals', $photo);
                $formattedPath = '/' . trim($uploadedPath, '/');
                $paths[] = $formattedPath;
            } else {
                Log::warning('Uploaded file is invalid.', ['file' => $photo->getClientOriginalName()]);
            }
        }
        return json_encode($paths);
    }
}
