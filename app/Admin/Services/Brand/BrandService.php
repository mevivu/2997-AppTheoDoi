<?php

namespace App\Admin\Services\Brand;

use App\Admin\Repositories\Brand\BrandRepositoryInterface;
use App\Api\V1\Support\UseLog;
use App\Enums\Brand\BrandStatus;
use Exception;
use Illuminate\Http\Request;
use App\Admin\Traits\Setup;

class BrandService implements BrandServiceInterface
{
    use Setup, UseLog;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected BrandRepositoryInterface $repository;

    public function __construct(
        BrandRepositoryInterface $repository,
    ) {
        $this->repository = $repository;
    }

    /**
     * Store a new brand.
     *
     * @throws Exception
     */
    public function store(Request $request): object|false
    {
        $data = $request->validated();
        return $this->repository->create($data);
    }

    /**
     * Update an existing brand.
     *
     * @throws Exception
     */
    public function update(Request $request): object|bool
    {
        $data = $request->validated();


        if (!BrandStatus::hasValue($data['status'])) {
            throw new \Exception("Invalid status value");
        }

        $data['status'] = BrandStatus::from($data['status'])->value;

        return $this->repository->update($data['id'], $data);
    }

    /**
     * Delete a brand.
     *
     * @throws Exception
     */
    public function delete($id): object
    {
        return $this->repository->delete($id);
    }

    /**
     * Perform actions on multiple brands (e.g. change status).
     *
     * @throws Exception
     */
    public function actionMultipleRecords(Request $request): bool
    {

        $this->data = $request->all();

        switch ($this->data['action']) {
            case BrandStatus::Active->value:
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', BrandStatus::Active);
                }
                return true;

            case BrandStatus::Draft->value:
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', BrandStatus::Draft);
                }
                return true;

            case BrandStatus::Deleted->value:
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', BrandStatus::Deleted);
                }
                return true;

            default:
                return false;
        }
    }

}
