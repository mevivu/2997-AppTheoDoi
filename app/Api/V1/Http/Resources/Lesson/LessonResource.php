<?php

namespace App\Api\V1\Http\Resources\Lesson;

use App\Enums\Lesson\EducationPillar;
use App\Enums\Lesson\LessonAccessType;
use App\Enums\Lesson\LessonDifficulty;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use JsonSerializable;
use Throwable;

class LessonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $isLocked = (bool) ($this->is_locked ?? false);

        // Lấy video đầu tiên hoặc video chính
        $video = $this->videos?->first() ?? $this->video;

        $videoData = null;
        if ($video) {
            $streamUrl = null;
            if (!$isLocked) {
                if ($video->isR2() && !empty($video->video_path)) {
                    try {
                        $streamUrl = Storage::disk('r2')->temporaryUrl(
                            $video->video_path,
                            now()->addMinutes(30)
                        );
                    } catch (Throwable $e) {
                        Log::warning('[LessonResource] R2 temporaryUrl failed: ' . $e->getMessage());
                        $streamUrl = $video->video_url;
                    }
                } else {
                    $streamUrl = $video->stream_url;
                }
            }

            $videoData = [
                'id' => $video->id,
                'title' => $video->title,
                'video_type' => $video->video_type?->value ?? 'youtube',
                'youtube_id' => $isLocked ? null : $video->youtube_id,
                'stream_url' => $streamUrl,
                'thumbnail_url' => $video->thumbnail_url,
                'duration_seconds' => (int) $video->duration_seconds,
                'formatted_duration' => $video->formatted_duration,
            ];
        }

        $difficultyKey = $this->difficulty instanceof LessonDifficulty ? $this->difficulty->value : (string) $this->difficulty;
        $difficultyLabel = $this->difficulty instanceof LessonDifficulty ? $this->difficulty->label() : LessonDifficulty::getDescription($this->difficulty);
        $difficultyBadge = $this->difficulty instanceof LessonDifficulty ? $this->difficulty->badge() : null;

        $accessTypeValue = $this->access_type instanceof LessonAccessType ? $this->access_type->value : (string) $this->access_type;
        $accessTypeLabel = $this->access_type instanceof LessonAccessType ? $this->access_type->label() : LessonAccessType::getDescription($this->access_type);

        $pillarValue = null;
        $pillarLabel = null;
        if ($this->category) {
            $pillarValue = $this->category->pillar instanceof EducationPillar ? $this->category->pillar->value : (string) $this->category->pillar;
            $pillarLabel = $this->category->pillar_label;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'thumbnail_url' => $this->thumbnail_url,
            'description' => $this->description,
            'difficulty' => [
                'key' => $difficultyKey,
                'label' => $difficultyLabel,
                'badge' => $difficultyBadge,
            ],
            'frequency' => $this->frequency,
            'benefit' => $this->benefit,
            'tools' => $this->tools,
            'access_type' => $accessTypeValue,
            'access_type_label' => $accessTypeLabel,
            'is_locked' => $isLocked,
            'view_count' => (int) $this->view_count,
            'sort_order' => (int) $this->sort_order,
            'category_id' => $this->lesson_category_id,
            'category_name' => $this->category?->name,
            'pillar' => $pillarValue,
            'pillar_label' => $pillarLabel,
            'age_group_id' => $this->age_group_id,
            'age_group_name' => $this->ageGroup?->name,
            'video_count' => (int) ($this->videos?->count() ?? 0),
            'video' => $videoData,
            // Thuộc tính tương thích ngược cho App phiên bản cũ
            'title' => $this->name,
            'video_url' => $videoData['stream_url'] ?? null,
            'youtube_id' => $videoData['youtube_id'] ?? null,
            'video_type' => $videoData['video_type'] ?? 'youtube',
            'duration_seconds' => $videoData['duration_seconds'] ?? 0,
            'formatted_duration' => $videoData['formatted_duration'] ?? '00:00',
            'video_category_id' => $this->lesson_category_id,
            'created_at' => format_datetime($this->created_at),
        ];
    }
}
