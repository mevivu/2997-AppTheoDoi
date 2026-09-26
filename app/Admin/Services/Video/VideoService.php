<?php

namespace App\Admin\Services\Video;

use App\Admin\Repositories\Video\VideoRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Enums\ActiveStatus;
use App\Traits\UseLog;
use Illuminate\Http\Request;

class VideoService implements VideoServiceInterface
{
    use UseLog;

    protected VideoRepositoryInterface $repository;
    protected FileService $fileService;

    public function __construct(
        VideoRepositoryInterface $repository,
        FileService $fileService
    ) {
        $this->repository = $repository;
        $this->fileService = $fileService;
    }

    public function store(Request $request)
    {
        $data = $request->validated();
        $data['is_preview'] = $request->boolean('is_preview');

        // Tự động nhận diện thời lượng YouTube nếu chưa nhập
        if (empty($data['duration_seconds']) && !empty($data['video_url'])) {
            $detectedDuration = \App\Models\Video::extractYouTubeDuration($data['video_url']);
            if ($detectedDuration) {
                $data['duration_seconds'] = $detectedDuration;
            }
        }

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->fileService
                ->setFolder('images/videos/thumbnails')
                ->setFile($request->file('thumbnail'))
                ->upload()
                ->getInstance();
        }

        return $this->repository->create($data);
    }

    public function update(Request $request)
    {
        $data = $request->validated();
        $data['is_preview'] = $request->boolean('is_preview');
        $video = $this->repository->findOrFail($data['id']);

        // Tự động nhận diện thời lượng YouTube nếu chưa nhập hoặc để trống
        if (empty($data['duration_seconds']) && !empty($data['video_url'])) {
            $detectedDuration = \App\Models\Video::extractYouTubeDuration($data['video_url']);
            if ($detectedDuration) {
                $data['duration_seconds'] = $detectedDuration;
            }
        }

        if ($request->hasFile('thumbnail')) {
            if (!empty($video->thumbnail)) {
                $this->fileService->delete($video->thumbnail);
            }
            $data['thumbnail'] = $this->fileService
                ->setFolder('images/videos/thumbnails')
                ->setFile($request->file('thumbnail'))
                ->upload()
                ->getInstance();
        }

        return $this->repository->update($data['id'], $data);
    }

    public function delete($id)
    {
        $video = $this->repository->findOrFail($id);
        if (!empty($video->thumbnail)) {
            $this->fileService->delete($video->thumbnail);
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
