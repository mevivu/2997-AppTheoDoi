<?php

namespace App\Admin\Services\Exercise;

use App\Admin\Repositories\Exercise\ExerciseRepositoryInterface;
use App\Admin\Repositories\ExerciseMedia\ExerciseMediaRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Enums\ActiveStatus;
use App\Enums\Exercise\ExerciseMediaType;
use App\Traits\UseLog;
use Illuminate\Http\Request;

class ExerciseService implements ExerciseServiceInterface
{
    use UseLog;

    protected ExerciseRepositoryInterface $repository;
    protected ExerciseMediaRepositoryInterface $mediaRepository;
    protected FileService $fileService;

    public function __construct(
        ExerciseRepositoryInterface $repository,
        ExerciseMediaRepositoryInterface $mediaRepository,
        FileService $fileService
    ) {
        $this->repository = $repository;
        $this->mediaRepository = $mediaRepository;
        $this->fileService = $fileService;
    }

    public function store(Request $request)
    {
        $data = $request->validated();
        $exercise = $this->repository->create($data);

        if ($exercise && $request->hasFile('media_files')) {
            $this->saveMediaFiles($exercise->id, $request);
        }

        return $exercise;
    }

    public function update(Request $request)
    {
        $data = $request->validated();
        $exercise = $this->repository->update($data['id'], $data);

        // Delete checked media
        if ($request->filled('delete_media_ids')) {
            foreach ($request->input('delete_media_ids') as $mediaId) {
                $media = $this->mediaRepository->find($mediaId);
                if ($media) {
                    if (!empty($media->media_file)) {
                        $this->fileService->delete($media->media_file);
                    }
                    if (!empty($media->thumbnail)) {
                        $this->fileService->delete($media->thumbnail);
                    }
                    $this->mediaRepository->delete($mediaId);
                }
            }
        }

        // Add new media
        if ($exercise && $request->hasFile('media_files')) {
            $this->saveMediaFiles($data['id'], $request);
        }

        return $exercise;
    }

    public function delete($id)
    {
        $exercise = $this->repository->findOrFail($id);

        // Delete all associated media files
        if ($exercise->media) {
            foreach ($exercise->media as $media) {
                if (!empty($media->media_file)) {
                    $this->fileService->delete($media->media_file);
                }
                if (!empty($media->thumbnail)) {
                    $this->fileService->delete($media->thumbnail);
                }
                $this->mediaRepository->delete($media->id);
            }
        }

        return $this->repository->delete($id);
    }

    protected function saveMediaFiles(int $exerciseId, Request $request): void
    {
        $files = $request->file('media_files', []);
        $types = $request->input('media_types', []);

        foreach ($files as $index => $file) {
            if (!$file->isValid()) {
                continue;
            }

            $type = $types[$index] ?? null;
            if (!$type) {
                $mime = $file->getMimeType();
                $type = str_starts_with($mime, 'video/') ? ExerciseMediaType::VIDEO->value : ExerciseMediaType::IMAGE->value;
            }

            $mediaPath = $this->fileService
                ->setFolder('images/exercises/media')
                ->setFile($file)
                ->upload()
                ->getInstance();

            $this->mediaRepository->create([
                'exercise_id' => $exerciseId,
                'media_type' => $type,
                'media_file' => $mediaPath,
                'sort_order' => $index,
            ]);
        }
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