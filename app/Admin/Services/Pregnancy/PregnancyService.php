<?php

namespace App\Admin\Services\Pregnancy;

use App\Admin\Repositories\Pregnancy\PregnancyRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use App\Enums\ActiveStatus;

class PregnancyService implements PregnancyServiceInterface
{

    protected array $data;
    protected PregnancyRepositoryInterface $repository;

    public function __construct(
        PregnancyRepositoryInterface $repository,

    )
    {
        $this->repository = $repository;

    }

    /**
     * @throws Exception
     */
    public function store(Request $request): object
    {
        $data = $request->validated();
        return $this->repository->create($data);

    }

    public function update(Request $request)
    {
        // TODO: Implement update() method.
        $data = $request->validated();
        return $this->repository->update($data['id'], $data);
    }

    public function delete($id): object
    {
        // TODO: Implement delete() method.
        return $this->repository->delete($id);
    }

    public function actionMultipleRecords(Request $request): bool
    {
        $this->data = $request->all();

        switch ($this->data['action']) {
            case ActiveStatus::Active->value:
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ActiveStatus::Active);
                }
                return true;
            case ActiveStatus::Draft->value:
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ActiveStatus::Draft);
                }
                return true;
            case ActiveStatus::Deleted->value:
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ActiveStatus::Deleted);
                }
                return true;

            default:
                return false;
        }
    }
}
