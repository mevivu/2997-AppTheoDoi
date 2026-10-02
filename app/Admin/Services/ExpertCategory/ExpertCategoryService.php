<?php

namespace App\Admin\Services\ExpertCategory;

use App\Admin\Repositories\ExpertCategory\ExpertCategoryRepositoryInterface;
use App\Api\V1\Support\UseLog;
use App\Enums\DefaultStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ExpertCategoryService implements ExpertCategoryServiceInterface
{
    use UseLog;

    protected ExpertCategoryRepositoryInterface $repository;

    public function __construct(ExpertCategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function store(Request $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $item = $this->repository->create($data);
            DB::commit();
            return $item;
        } catch (Throwable $e) {
            DB::rollBack();
            $this->logError('Failed to create expert category CMS', $e);
            return false;
        }
    }

    public function update(Request $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $item = $this->repository->update($data['id'], $data);
            DB::commit();
            return $item;
        } catch (Throwable $e) {
            DB::rollBack();
            $this->logError('Failed to update expert category CMS', $e);
            return false;
        }
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function actionMultipleRecode(Request $request): bool
    {
        $data = $request->all();
        if (empty($data['id']) || !is_array($data['id'])) {
            return false;
        }

        switch ($data['action']) {
            case 1:
                foreach ($data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', DefaultStatus::Published);
                }
                return true;
            case 2:
                foreach ($data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', DefaultStatus::Draft);
                }
                return true;
            default:
                return false;
        }
    }
}
