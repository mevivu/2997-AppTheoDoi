<?php

namespace App\Admin\Services\Children;

use App\Admin\Repositories\Children\ChildrenRepositoryInterface;
use App\Admin\Traits\Roles;
use App\Api\V1\Support\UseLog;
use App\Enums\Child\BornStatus;
use App\Enums\Child\ChildStatus;
use Exception;
use Illuminate\Http\Request;
use App\Admin\Traits\Setup;
use Illuminate\Support\Carbon;

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

        if ($data['is_born'] == BornStatus::Born->value) {
            $data['due_date'] = null;
            $birthday = $data['birthday'];
            $birthday = new Carbon($birthday);
            $currentDate = Carbon::now();
            $month = floor($currentDate->diffInDays($birthday) / 30.5);
            $age = $currentDate->diffInDays($birthday) / 365.3;
            $data['age'] = $age;
            $data['month'] = $month;
        } elseif ($data['is_born'] == BornStatus::Unborn->value) {
            $data['birthday'] = null;
            $data['age'] = null;
            $data['month'] = null;
        }
        return $this->repository->create($data);
    }

    /**
     * @throws Exception
     */
    public function update(Request $request): object|bool
    {

        $data = $request->validated();

        if ($data['is_born'] == BornStatus::Born->value) {
            $data['due_date'] = null;
            $birthday = $data['birthday'];
            $birthday = new Carbon($birthday);
            $currentDate = Carbon::now();
            $month = $currentDate->diffInDays($birthday) / 30.5;
            $age = $currentDate->diffInDays($birthday) / 365.3;
            $data['age'] = $age;
            $data['month'] = $month;
        } elseif ($data['is_born'] == BornStatus::Unborn->value) {
            $data['birthday'] = null;
            $data['age'] = null;
            $data['month'] = null;
        }

        return $this->repository->update($data['id'], $data);
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
