<?php

namespace App\Admin\Services\WeightHeightWho;

use App\Admin\Repositories\WeightHeightWho\WeightHeightWhoRepositoryInterface;
use App\Admin\Services\Permission\PermissionServiceInterface;
use App\Admin\Repositories\Permission\PermissionRepositoryInterface;
use App\Enums\ActiveStatus;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class WeightHeightWhoService implements WeightHeightWhoServiceInterface
{
    /**
     * Current Object instance
     *
     * @var array
     */
    protected $data;

    protected $repository;

    public function __construct(WeightHeightWhoRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function store(Request $request)
    {
        // TODO: Implement store() method.
        $data=$request->validated();
        return $this->repository->create($data);
    }

    public function update(Request $request)
    {
        // TODO: Implement update() method.
        $data=$request->validated();
        return $this->repository->update($data['id'], $data);
    }

    public function actionMultipleRecords(Request $request):bool
    {
        // TODO: Implement actionMultipleRecords() method.
        $data = $request->all();

        switch ($data['action']) {
            case ActiveStatus::Active->value:
                foreach ($data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ActiveStatus::Active);
                }
                return true;
            case ActiveStatus::Draft->value:
                foreach ($data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ActiveStatus::Draft);
                }
                return true;
            case ActiveStatus::Deleted->value:
                foreach ($data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ActiveStatus::Deleted);
                }
                return true;

            default:
                return false;
        }
    }
}
