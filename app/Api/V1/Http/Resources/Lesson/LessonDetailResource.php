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

class LessonDetailResource extends JsonResource
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

        // Xử lý toàn bộ danh sách video của bài học
        $videosList = [];
        if ($this->videos) {
            foreach ($this->videos as $v) {
                $streamUrl = null;
                if (!$isLocked) {
                    if ($v->isR2() && !empty($v->video_path)) {
                        try {
                            $streamUrl = Storage::disk('r2')->temporaryUrl(
                                $v->video_path,
                                now()->addMinutes(30)
                            );
                        } catch (Throwable $e) {
                            Log::warning('[LessonDetailResource] R2 temporaryUrl failed: ' . $e->getMessage());
                            $streamUrl = $v->video_url;
                        }
                    } else {
                        $streamUrl = $v->stream_url;
                    }
                }

                $videosList[] = [
                    'id' => $v->id,
                    'title' => $v->title,
                    'video_type' => $v->video_type?->value ?? 'youtube',
                    'youtube_id' => $isLocked ? null : $v->youtube_id,
                    'stream_url' => $streamUrl,
                    'thumbnail_url' => $v->thumbnail_url,
                    'duration_seconds' => (int) $v->duration_seconds,
                    'formatted_duration' => $v->formatted_duration,
                    'sort_order' => (int) $v->sort_order,
                ];
            }
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
            'content' => $this->content,
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
            'age_group_id' => $this->category?->age_group_id,
            'age_group_name' => $this->category?->ageGroup?->name,
            'videos' => $videosList,
            // Thuộc tính tương thích ngược cho App phiên bản cũ
            'title' => $this->name,
            'video_url' => $videosList[0]['stream_url'] ?? null,
            'youtube_id' => $videosList[0]['youtube_id'] ?? null,
            'video_type' => $videosList[0]['video_type'] ?? 'youtube',
            'duration_seconds' => $videosList[0]['duration_seconds'] ?? 0,
            'formatted_duration' => $videosList[0]['formatted_duration'] ?? '00:00',
            'video_category_id' => $this->lesson_category_id,
            'created_at' => format_datetime($this->created_at),
        ];
    }
}
