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
        $qualifications = $data['qualifications'] ?? [];
        unset($data['qualifications']);
        $data['is_verified'] = !empty($data['is_verified']);

        DB::beginTransaction();
        try {
            $item = $this->repository->create($data);
            $this->syncQualifications($item, $qualifications);
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
        $qualifications = $data['qualifications'] ?? [];
        unset($data['qualifications']);
        $data['is_verified'] = !empty($data['is_verified']);

        DB::beginTransaction();
        try {
            $item = $this->repository->update($data['id'], $data);
            $this->syncQualifications($item, $qualifications);
            DB::commit();
            return $item;
        } catch (Throwable $e) {
            DB::rollBack();
            $this->logError('Failed to update expert CMS', $e);
            return false;
        }
    }

    /**
     * Đồng bộ danh sách học vị & bằng cấp chuyên môn của chuyên gia
     */
    protected function syncQualifications($expert, array $qualifications): void
    {
        if (!$expert) {
            return;
        }

        $expert->qualifications()->delete();

        foreach ($qualifications as $index => $item) {
            if (empty($item['degree_name']) || trim($item['degree_name']) === '') {
                continue;
            }
            $expert->qualifications()->create([
                'degree_name' => trim($item['degree_name']),
                'institution' => !empty($item['institution']) ? trim($item['institution']) : null,
                'graduation_year' => !empty($item['graduation_year']) ? trim($item['graduation_year']) : null,
                'specialization' => !empty($item['specialization']) ? trim($item['specialization']) : null,
                'sort_order' => isset($item['sort_order']) ? (int) $item['sort_order'] : $index,
                'status' => DefaultStatus::Published->value,
            ]);
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
