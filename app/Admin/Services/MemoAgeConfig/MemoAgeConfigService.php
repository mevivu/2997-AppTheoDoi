<?php

namespace App\Admin\Services\MemoAgeConfig;

use App\Admin\Repositories\MemoAgeConfig\MemoAgeConfigRepositoryInterface;
use App\Enums\ActiveStatus;
use Illuminate\Http\Request;

class MemoAgeConfigService implements MemoAgeConfigServiceInterface
{
    protected $repository;

    public function __construct(
        MemoAgeConfigRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function store(Request $request)
    {
        $data = $request->validated();
        $rows = (int) $data['rows'];
        $cols = (int) $data['columns'];
        $data['total_cards'] = $rows * $cols;
        $data['pairs_count'] = (int) ($data['total_cards'] / 2);
        return $this->repository->create($data);
    }

    public function update(Request $request)
    {
        $data = $request->validated();
        $rows = (int) $data['rows'];
        $cols = (int) $data['columns'];
        $data['total_cards'] = $rows * $cols;
        $data['pairs_count'] = (int) ($data['total_cards'] / 2);
        return $this->repository->update($data['id'], $data);
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
