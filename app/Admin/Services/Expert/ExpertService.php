<?php

namespace App\Admin\Services\Expert;

use App\Admin\Repositories\Expert\ExpertRepositoryInterface;
use App\Api\V1\Support\UseLog;
use App\Enums\DefaultStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ExpertService implements ExpertServiceInterface
{
    use UseLog;

    protected ExpertRepositoryInterface $repository;

    public function __construct(ExpertRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function store(Request $request)
    {
        $data = $request->validated();
        $data['is_verified'] = !empty($data['is_verified']);
        DB::beginTransaction();
        try {
            $item = $this->repository->create($data);
            DB::commit();
            return $item;
        } catch (Throwable $e) {
            DB::rollBack();
            $this->logError('Failed to create expert CMS', $e);
            return false;
        }
    }

    public function update(Request $request)
    {
        $data = $request->validated();
        $data['is_verified'] = !empty($data['is_verified']);
        DB::beginTransaction();
        try {
            $item = $this->repository->update($data['id'], $data);
            DB::commit();
            return $item;
        } catch (Throwable $e) {
            DB::rollBack();
            $this->logError('Failed to update expert CMS', $e);
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
