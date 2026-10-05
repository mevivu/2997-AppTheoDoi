<?php

namespace App\Admin\Services\Classes;

use App\Admin\Repositories\Classes\ClassesRepositoryInterface;
use App\Api\V1\Support\UseLog;
use App\Enums\ActiveStatus;
use App\Enums\Class\EducationLevel;
use App\Enums\Class\LevelGroup;
use App\Enums\ReportCard\EvaluationMethod;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Admin\Traits\Setup;

class ClassesService implements ClassesServiceInterface
{
    use Setup, UseLog;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected ClassesRepositoryInterface $repository;


    public function __construct(
        ClassesRepositoryInterface $repository,
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
        [$subjectIds, $config] = $this->extractSubjects($data);
        $data = $this->withDerivedLevelGroup($data);

        $class = $this->repository->create($data);

        $level = $class->resolvedEducationLevel();
        $class->subjects()->sync($this->buildSyncData($subjectIds, $config, $level, collect()));

        return $class;
    }

    public function update(Request $request): object|bool
    {
        $data = $request->validated();
        [$subjectIds, $config] = $this->extractSubjects($data);
        $data = $this->withDerivedLevelGroup($data);

        $class = $this->repository->update($data['id'], $data);

        $existing = $class->subjects()->get()->keyBy('id');
        $level = $class->resolvedEducationLevel();
        $class->subjects()->sync($this->buildSyncData($subjectIds, $config, $level, $existing));

        return $class;
    }

    /**
     * @return array{0: int[], 1: array<int|string, array>}
     */
    private function extractSubjects(array &$data): array
    {
        $subjectIds = array_values(array_unique(array_map('intval', $data['subject_id'] ?? [])));
        $config = $data['subject_config'] ?? [];
        unset($data['subject_id'], $data['subject_config']);

        return [$subjectIds, $config];
    }

    /**
     * Bỏ các trường null để không ghi đè dữ liệu cũ; tự suy ra level_group từ cấp học nếu thiếu.
     */
    private function withDerivedLevelGroup(array $data): array
    {
        $data = array_filter($data, fn ($v) => $v !== null);

        if (!isset($data['level_group']) && isset($data['education_level'])) {
            $data['level_group'] = $data['education_level'] === EducationLevel::Primary->value
                ? LevelGroup::Junior->value
                : LevelGroup::Senior->value;
        }

        return $data;
    }

    /**
     * Ưu tiên: cấu hình gửi lên > cấu hình đang lưu > mặc định theo cấp học.
     */
    private function buildSyncData(array $subjectIds, array $config, EducationLevel $level, Collection $existing): array
    {
        $default = EvaluationMethod::defaultFor($level)->value;
        $sync = [];

        foreach ($subjectIds as $index => $subjectId) {
            $cfg = $config[$subjectId] ?? $config[(string) $subjectId] ?? [];
            $old = $existing->get($subjectId)?->pivot;

            $method = $cfg['evaluation_method'] ?? null;
            $sortOrder = $cfg['sort_order'] ?? null;

            $sync[$subjectId] = [
                'evaluation_method' => ($method !== null && $method !== '')
                    ? $method
                    : ($old?->evaluation_method ?: $default),
                'is_required' => array_key_exists('is_required', $cfg) && $cfg['is_required'] !== null
                    ? (bool) $cfg['is_required']
                    : (bool) ($old?->is_required ?? true),
                'sort_order' => ($sortOrder !== null && $sortOrder !== '')
                    ? (int) $sortOrder
                    : ($old?->sort_order ?? $index + 1),
            ];
        }

        return $sync;
    }


    /**
     * @throws Exception
     */
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
}
