<?php

namespace App\Admin\Services\Package;

use App\Admin\Repositories\Package\PackageRepositoryInterface;
use App\Api\V1\Support\UseLog;
use App\Enums\Package\PackageStatus;
use Exception;
use Illuminate\Http\Request;
use App\Admin\Traits\Setup;

class PackageService implements PackageServiceInterface
{
    use Setup, UseLog;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected PackageRepositoryInterface $repository;


    public function __construct(
        PackageRepositoryInterface $repository,
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
        $data['status'] = PackageStatus::Draft;
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
            case PackageStatus::Active->value:
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', PackageStatus::Active);
                }
                return true;
            case PackageStatus::Draft->value:
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', PackageStatus::Draft);
                }
                return true;
            case PackageStatus::Deleted->value:
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', PackageStatus::Deleted);
                }
                return true;

            default:
                return false;
        }
    }
}
