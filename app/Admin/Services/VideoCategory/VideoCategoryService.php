<?php

namespace App\Admin\Services\VideoCategory;

use App\Admin\Repositories\VideoCategory\VideoCategoryRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Enums\ActiveStatus;
use App\Traits\UseLog;
use Illuminate\Http\Request;

class VideoCategoryService implements VideoCategoryServiceInterface
{
    use UseLog;

    protected VideoCategoryRepositoryInterface $repository;
    protected FileService $fileService;

    public function __construct(
        VideoCategoryRepositoryInterface $repository,
        FileService $fileService
    ) {
        $this->repository = $repository;
        $this->fileService = $fileService;
    }

    public function store(Request $request)
    {
        $data = $request->validated();

        if ($request->hasFile('icon')) {
            $data['icon'] = $this->fileService
                ->setFolder('images/videos/categories')
                ->setFile($request->file('icon'))
                ->upload()
                ->getInstance();
        }

        return $this->repository->create($data);
    }

    public function update(Request $request)
    {
        $data = $request->validated();
        $category = $this->repository->findOrFail($data['id']);

        if ($request->hasFile('icon')) {
            $this->fileService->delete($category->icon);
            $data['icon'] = $this->fileService
                ->setFolder('images/videos/categories')
                ->setFile($request->file('icon'))
                ->upload()
                ->getInstance();
        }

        return $this->repository->update($data['id'], $data);
    }

    public function delete($id)
    {
        $category = $this->repository->findOrFail($id);
        if (!empty($category->icon)) {
            $this->fileService->delete($category->icon);
        }
        return $this->repository->delete($id);
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
