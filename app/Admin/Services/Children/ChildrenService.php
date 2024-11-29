<?php

namespace App\Admin\Services\Children;

use App\Admin\Repositories\Children\ChildrenRepositoryInterface;
use App\Admin\Traits\Roles;
use App\Api\V1\Support\UseLog;
use App\Enums\Child\ChildStatus;
use Exception;
use Illuminate\Http\Request;
use App\Admin\Traits\Setup;

class ChildrenService implements ChildrenServiceInterface
{
    use Setup, Roles, UseLog;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected ChildrenRepositoryInterface $repository;


    public function __construct(
        ChildrenRepositoryInterface $repository,
    )
    {
        $this->repository = $repository;
    }

    /**
     * @throws Exception
     */
    public function store(Request $request): object|false
    {
        $data = $request->validated();        
        $children = $this->repository->create($data);
        return $children;
    }

    /**
     * @throws Exception
     */
    public function update(Request $request): object|bool
    {

        $data = $request->validated();
        $children = $this->repository->update($data['id'], $data);
        return $children;
    }

    /**
     * @throws Exception
     */
    public function delete($id): object
    {
        return $this->repository->delete($id);

    }

    public function actionMultipleRecode(Request $request): bool
    {
        $this->data = $request->all();

        switch ($this->data['action']) {
            case 'active':
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ChildStatus::Active);
                }
                return true;
            case 'draft':
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ChildStatus::Draft);
                }
                return true;
            case 'deleted':
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ChildStatus::Deleted);
                }
                return true;

            default:
                return false;
        }
    }

}
