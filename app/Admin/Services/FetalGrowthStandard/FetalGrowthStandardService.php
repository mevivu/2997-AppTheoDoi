<?php

namespace App\Admin\Services\FetalGrowthStandard;

use App\Admin\Repositories\FetalGrowthStandard\FetalGrowthStandardRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Traits\UseLog;
use Illuminate\Http\Request;

class FetalGrowthStandardService implements FetalGrowthStandardServiceInterface
{
    use UseLog;

    protected FetalGrowthStandardRepositoryInterface $repository;

    public function __construct(FetalGrowthStandardRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function store(Request $request)
    {
        $data = $request->validated();
        return $this->repository->create($data);
    }

    public function update(Request $request)
    {
        $data = $request->validated();
        return $this->repository->update($data['id'], $data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function actionMultipleRecords(Request $request): bool
    {
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
