<?php

namespace App\Admin\Services\ExerciseMedia;

use App\Admin\Repositories\ExerciseMedia\ExerciseMediaRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Traits\UseLog;
use Illuminate\Http\Request;

class ExerciseMediaService implements ExerciseMediaServiceInterface
{
    use UseLog;

    protected ExerciseMediaRepositoryInterface $repository;
    protected FileService $fileService;

    public function __construct(
        ExerciseMediaRepositoryInterface $repository,
        FileService $fileService
    ) {
        $this->repository = $repository;
        $this->fileService = $fileService;
    }

    public function store(Request $request)
    {
        $data = $request->validated();

        if ($request->hasFile('media_file')) {
            $data['media_file'] = $this->fileService
                ->setFolder('images/exercises/media')
                ->setFile($request->file('media_file'))
                ->upload()
                ->getInstance();
        }

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->fileService
                ->setFolder('images/exercises/thumbnails')
                ->setFile($request->file('thumbnail'))
                ->upload()
                ->getInstance();
        }

        return $this->repository->create($data);
    }

    public function delete($id)
    {
        $media = $this->repository->findOrFail($id);
        if (!empty($media->media_file)) {
            $this->fileService->delete($media->media_file);
        }
        if (!empty($media->thumbnail)) {
            $this->fileService->delete($media->thumbnail);
        }
        return $this->repository->delete($id);
    }
}
