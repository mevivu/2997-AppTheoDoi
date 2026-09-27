<?php

namespace App\Admin\Services\Lesson;

use App\Admin\Repositories\Lesson\LessonRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Enums\ActiveStatus;
use App\Models\Lesson;
use App\Models\LessonVideo;
use App\Traits\UseLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LessonService implements LessonServiceInterface
{
    use UseLog;

    protected LessonRepositoryInterface $repository;
    protected FileService $fileService;

    public function __construct(
        LessonRepositoryInterface $repository,
        FileService $fileService
    ) {
        $this->repository = $repository;
        $this->fileService = $fileService;
    }

    public function store(Request $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $this->fileService->uploadAvatar('images/lessons', $request->file('image'));
        } else {
            unset($data['image']);
        }

        return DB::transaction(function () use ($data, $request) {
            $lesson = $this->repository->create($data);

            $this->syncVideos($lesson, $request);

            return $lesson;
        });
    }

    public function update(Request $request)
    {
        $data = $request->validated();
        $id = $data['id'] ?? $request->input('id');
        $currentLesson = $this->repository->findOrFail($id);

        if ($request->hasFile('image')) {
            $data['image'] = $this->fileService->uploadAvatar('images/lessons', $request->file('image'), $currentLesson->image);
        } elseif ($request->input('reset_image') == '1') {
            if (!empty($currentLesson->image)) {
                $this->fileService->delete($currentLesson->image);
            }
            $data['image'] = null;
        } else {
            unset($data['image']);
        }

        return DB::transaction(function () use ($id, $data, $request) {
            $lesson = $this->repository->update($id, $data);

            $this->syncVideos($lesson, $request);

            return $lesson;
        });
    }

    public function delete($id)
    {
        $lesson = $this->repository->findOrFail($id);

        return DB::transaction(function () use ($lesson, $id) {
            // Dọn dẹp ảnh đại diện bài học
            if (!empty($lesson->image)) {
                $this->fileService->delete($lesson->image);
            }

            // Dọn dẹp các video R2
            foreach ($lesson->videos as $video) {
                if ($video->isR2() && !empty($video->video_path)) {
                    try {
                        $this->fileService->deleteR2File($video->video_path);
                    } catch (\Exception $e) {
                        $this->logError('Lỗi xóa file R2: ' . $e->getMessage());
                    }
                }
                if (!empty($video->thumbnail) && !str_starts_with($video->thumbnail, 'http')) {
                    $this->fileService->delete($video->thumbnail);
                }
            }

            return $this->repository->delete($id);
        });
    }

    /**
     * Đồng bộ danh sách video (Hỗ trợ 2 hoặc nhiều video)
     */
    protected function syncVideos(Lesson $lesson, Request $request): void
    {
        $videosData = $request->input('videos', []);
        if (!is_array($videosData)) {
            $videosData = [];
        }

        $existingVideoIds = $lesson->videos()->pluck('id')->toArray();
        $processedVideoIds = [];

        foreach ($videosData as $index => $item) {
            $videoUrl = trim($item['video_url'] ?? '');
            $videoPath = trim($item['video_path'] ?? '');
            $fileKey = "videos.{$index}.video_file";
            $hasFile = $request->hasFile($fileKey);

            if (empty($videoUrl) && empty($videoPath) && !$hasFile) {
                continue;
            }

            $videoId = !empty($item['id']) ? (int) $item['id'] : null;
            // Nếu không có videoId từ form nhưng bài học đã có video trong DB -> gắn ID để cập nhật
            if (!$videoId && !empty($existingVideoIds)) {
                $videoId = $existingVideoIds[0];
            }

            $videoType = $item['video_type'] ?? 'youtube';
            $durationSeconds = !empty($item['duration_seconds']) ? (int) $item['duration_seconds'] : 0;
            $thumbnail = $item['thumbnail'] ?? null;

            // Xử lý tự động lấy thời lượng YouTube nếu thiếu
            if ($videoType === 'youtube' && !empty($videoUrl) && $durationSeconds <= 0) {
                $detected = \App\Models\Video::extractYouTubeDuration($videoUrl);
                if ($detected) {
                    $durationSeconds = $detected;
                }
            }

            // Tự động bổ sung thumbnail YouTube nếu thiếu
            if ($videoType === 'youtube' && !empty($videoUrl) && empty($thumbnail)) {
                $ytId = LessonVideo::extractYouTubeId($videoUrl);
                if ($ytId) {
                    $thumbnail = "https://img.youtube.com/vi/{$ytId}/hqdefault.jpg";
                }
            }

            // Xử lý upload file video R2 từ form repeater (nếu có file gửi kèm)
            if ($hasFile) {
                $uploadResult = $this->fileService->uploadVideoToR2(
                    $request->file($fileKey),
                    'videos/lessons'
                );
                $videoPath = $uploadResult['path'];
                $videoUrl = $uploadResult['url'];
            }

            $payload = [
                'lesson_id' => $lesson->id,
                'title' => !empty($item['title']) ? $item['title'] : ($lesson->name ?? ('Video ' . ($index + 1))),
                'video_type' => $videoType,
                'video_url' => $videoUrl,
                'video_path' => $videoPath,
                'thumbnail' => $thumbnail,
                'duration_seconds' => $durationSeconds,
                'sort_order' => $item['sort_order'] ?? ($index + 1),
            ];

            if ($videoId && in_array($videoId, $existingVideoIds)) {
                $v = LessonVideo::find($videoId);
                if ($v) {
                    $v->update($payload);
                    $processedVideoIds[] = $videoId;
                }
            } else {
                $newVideo = LessonVideo::create($payload);
                $processedVideoIds[] = $newVideo->id;
            }
        }

        // Xóa các video bị quản trị viên gỡ bỏ khỏi form
        $toDeleteIds = array_diff($existingVideoIds, $processedVideoIds);
        if (!empty($toDeleteIds)) {
            $toDeleteVideos = LessonVideo::whereIn('id', $toDeleteIds)->get();
            foreach ($toDeleteVideos as $delV) {
                if ($delV->isR2() && !empty($delV->video_path)) {
                    try {
                        $this->fileService->deleteR2File($delV->video_path);
                    } catch (\Exception $e) {
                        // ignore
                    }
                }
                $delV->delete();
            }
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
