<?php

namespace App\Api\V1\Services\Child;

use App\Admin\Services\File\FileService;
use App\Api\V1\Repositories\Child\ChildRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use App\Enums\Child\BornStatus;
use App\Enums\Child\ChildStatus;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;


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
        if ($data['is_born'] == BornStatus::Born->value) {
            $birthday = $data['birthday'];
            $birthday = new Carbon($birthday);
            $currentDate = Carbon::now();
            $month = $currentDate->diffInDays($birthday) / 30.5;
            $age = $currentDate->diffInDays($birthday) / 365.3;
            $data['age'] = $age;
            $data['month'] = $month;
        }
        if ($avatar) {
            $data['avatar'] = $this->fileService
                ->uploadAvatar('images/children', $avatar);
        }
        return $this->repository->create($data);
    }

    /**
     * @throws Exception
     */
    public function update(Request $request): object
    {
        $data = $request->validated();
        $child = $this->repository->find($data['id']);
        $avatar = $data['avatar'];
        $birthday = $data['birthday'];
        if (isset($birthday)) {
            $birthday = new Carbon($birthday);
            $currentDate = Carbon::now();
            $month = $currentDate->diffInDays($birthday) / 30.5;
            $age = $currentDate->diffInDays($birthday) / 365.3;
            $data['age'] = $age;
            $data['month'] = $month;
        }
        if ($avatar) {
            $data['avatar'] = $this->fileService
                ->uploadAvatar('images/children', $avatar, $child->avatar);
        }

        return $this->repository->update($data['id'], $data);
    }

    public function index(Request $request)
    {
        $data = $request->validated();
        $page = $data['page'] ?? 1;
        $limit = $data['limit'] ?? 10;
        $query = $this->repository->getByQueryBuilder([
            'status' => ChildStatus::Active,
            'user_id' => $this->getCurrentUserId(),
        ]);
        return $query->paginate($limit, ['*'], 'page', $page);
    }
}
