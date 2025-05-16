<?php

namespace App\Admin\Services\Guide;


use App\Admin\Repositories\Guide\GuideRepositoryInterface;
use App\Api\V1\Support\UseLog;
use App\Enums\ActiveStatus;
use Exception;
use Illuminate\Http\Request;
use App\Admin\Traits\Setup;

class GuideService implements GuideServiceInterface
{
    use Setup, UseLog;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected GuideRepositoryInterface $repository;



    public function __construct(
        GuideRepositoryInterface   $repository,

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
    public function storeSteps(object $guide, array $steps): void
    {
        foreach ($steps as $step) {
            $guide->steps()->create([
                'title' => $step['title'],
                'description' => $step['description'] ?? null,
                'order' => $step['order'],
            ]);
        }
    }

    public function updateSteps(object $guide, array $steps): void
    {
        $guide->steps()->delete();
        $this->storeSteps($guide, $steps);
    }
}
