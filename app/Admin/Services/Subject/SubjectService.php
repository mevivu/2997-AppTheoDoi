<?php

namespace App\Admin\Services\Subject;

use App\Admin\Repositories\Subject\SubjectRepositoryInterface;
use Illuminate\Http\Request;
use App\Enums\ActiveStatus;

class SubjectService implements SubjectServiceInterface
{
    protected $repository;

    public function __construct(
        SubjectRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function store(Request $request)
    {
        $data = $request->validated();
        $class_ids = $data['class_id'];
        unset($data['class_id']);
        $subject = $this->repository->create($data);

        $subject->classes()->attach($class_ids);

        return $subject;
    }

    public function update(Request $request)
    {
        $data = $request->validated();
        $class_ids = $data['class_id'];
        unset($data['class_id']);
        $subject = $this->repository->update($data['id'], $data);

        $subject->classes()->sync($class_ids);

        return $subject;
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