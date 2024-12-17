<?php

namespace App\Api\V1\Services\Child;

use App\Admin\Services\File\FileService;
use App\Api\V1\Repositories\Child\ChildRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use Exception;
use Illuminate\Http\Request;


class ChildService implements ChildServiceInterface
{
    use AuthSupport, AuthServiceApi;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected ChildRepositoryInterface $repository;

    protected FileService $fileService;


    public function __construct(
        ChildRepositoryInterface $repository,
        FileService              $fileService
    )
    {
        $this->repository = $repository;
        $this->fileService = $fileService;

    }

    /**
     * @throws Exception
     */
    public function store(Request $request): object
    {
        $data = $request->validated();
        $avatar = $data['avatar'];
        $data['user_id'] = $this->getCurrentUserId();
        if ($avatar) {
            $data['avatar'] = $this->fileService
                ->uploadAvatar('images/children', $avatar);
        }
        return $this->repository->create($data);
    }
}
