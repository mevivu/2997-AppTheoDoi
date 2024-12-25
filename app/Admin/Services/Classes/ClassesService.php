<?php

namespace App\Admin\Services\Classes;

use App\Admin\Repositories\Classes\ClassesRepositoryInterface;
use App\Api\V1\Support\UseLog;
use App\Enums\ActiveStatus;
use Exception;
use Illuminate\Http\Request;
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

       $class= $this->repository->create($data);
        if(!empty($data['subject_id'])){
            $class->subjects()->attach($data['subject_id']);
        }
        return $class;
    }

    public function update(Request $request): object|bool
    {
        $data = $request->validated();
        $class=$this->repository->findOrFail($data['id']);
        $update=$this->repository->update($data['id'],[
            'name'=>$data['name'],
            'status'=>$data['status'],
        ]);
        if ($update && !empty($data['subject_id'])) {
            $class->subjects()->sync($data['subject_id']);
        }
        return $update;
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
