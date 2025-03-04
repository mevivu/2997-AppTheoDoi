<?php

namespace App\Admin\Services\VaccinationSchedule;

use App\Admin\Repositories\Children\ChildrenRepositoryInterface;
use App\Admin\Repositories\VaccinationSchedule\VaccinationScheduleRepository;
use App\Admin\Repositories\VaccinationSchedule\VaccinationScheduleRepositoryInterface;
use App\Api\V1\Support\UseLog;
use App\Enums\ActiveStatus;
use App\Enums\Permission\PermissionType;
use Exception;
use Illuminate\Http\Request;
use App\Admin\Traits\Setup;

class VaccinationScheduleSizeService implements VaccinationScheduleServiceInterface
{
    use Setup, UseLog;


    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;
    protected ChildrenRepositoryInterface $childrenRepository;

    protected VaccinationScheduleRepositoryInterface $repository;


    public function __construct(
        VaccinationScheduleRepository $repository,
        ChildrenRepositoryInterface   $childrenRepository
    )
    {
        $this->repository = $repository;
        $this->childrenRepository = $childrenRepository;

    }


    /**
     * @throws Exception
     */
    public function store(Request $request): object|false
    {
        $data = $request->validated();
        $data['type'] = PermissionType::ADMIN;
        return $this->repository->create($data);
    }

    /**
     * @throws Exception
     */
    public function update(Request $request): object|bool
    {

        $data = $request->validated();
        return $this->repository->update($data['id'], $data);
    }

    /**
     * @throws Exception
     */
    public function delete($id): object
    {
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
