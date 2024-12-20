<?php

namespace App\Admin\Services\Pregnancy;

use App\Admin\Repositories\Pregnancy\PregnancyRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use App\Enums\ActiveStatus;

class PregnancyService implements PregnancyServiceInterface
{
    protected PregnancyRepositoryInterface $repository;
    protected PregnancyServiceInterface $answerRepository;

    public function __construct(
        PregnancyRepositoryInterface $repository,
        PregnancyServiceInterface    $answerRepository
    )
    {
        $this->repository = $repository;
        $this->answerRepository = $answerRepository;
    }

    /**
     * @throws Exception
     */
    public function store(Request $request): object
    {
        $data = $request->validated();
        return $this->repository->create($data['question']);

    }

    /**
     * @throws Exception
     */
    public function update(Request $request): object
    {
        $data = $request->validated();

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
