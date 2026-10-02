<?php

namespace App\Admin\Services\ExpertPost;

use App\Admin\Repositories\ExpertPost\ExpertPostRepositoryInterface;
use App\Api\V1\Support\UseLog;
use App\Enums\DefaultStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ExpertPostService implements ExpertPostServiceInterface
{
    use UseLog;

    protected ExpertPostRepositoryInterface $repository;

    public function __construct(ExpertPostRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function store(Request $request)
    {
        $data = $request->validated();
        $data['posted_at'] = $data['posted_at'] ?? now();
        $data['is_featured'] = !empty($data['is_featured']) ? 1 : 0;
        DB::beginTransaction();
        try {
            $item = $this->repository->create($data);
            DB::commit();
            return $item;
        } catch (Throwable $e) {
            DB::rollBack();
            $this->logError('Failed to create expert post CMS', $e);
            return false;
        }
    }

    public function update(Request $request)
    {
        $data = $request->validated();
        $data['is_featured'] = !empty($data['is_featured']) ? 1 : 0;
        DB::beginTransaction();
        try {
            $item = $this->repository->update($data['id'], $data);
            DB::commit();
            return $item;
        } catch (Throwable $e) {
            DB::rollBack();
            $this->logError('Failed to update expert post CMS', $e);
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
