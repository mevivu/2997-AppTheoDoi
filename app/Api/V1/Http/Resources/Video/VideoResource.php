<?php

namespace App\Api\V1\Http\Resources\Video;

use App\Enums\Video\VideoAccessType;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use JsonSerializable;
use Throwable;

class VideoResource extends JsonResource
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

        // Xử lý link video an toàn
        $videoUrl = null;
        if (!$isLocked) {
            if ($this->isR2() && !empty($this->video_path)) {
                try {
                    // Tạo Presigned URL Cloudflare R2 thời hạn 30 phút
                    $videoUrl = Storage::disk('r2')->temporaryUrl(
                        $this->video_path,
                        now()->addMinutes(30)
                    );
                } catch (Throwable $e) {
                    Log::warning('[VideoResource] Failed to generate R2 temporaryUrl: ' . $e->getMessage());
                    $videoUrl = $this->video_url;
                }
            } else {
                $videoUrl = $this->video_url;
            }
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'youtube_id' => $isLocked ? null : $this->youtube_id,
            'video_url' => $videoUrl,
            'thumbnail_url' => $this->thumbnail_url,
            'video_type' => $this->video_type?->value ?? 'youtube',
            'duration_seconds' => (int) $this->duration_seconds,
            'formatted_duration' => $this->formatted_duration,
            'access_type' => $this->access_type?->value,
            'access_type_label' => $this->access_type ? VideoAccessType::getDescription($this->access_type->value) : null,
            'is_preview' => (bool) $this->is_preview,
            'is_locked' => $isLocked,
            'view_count' => (int) $this->view_count,
            'video_category_id' => $this->video_category_id,
            'category_name' => $this->category?->name,
            'sort_order' => (int) $this->sort_order,
            'created_at' => format_datetime($this->created_at),
        ];
    }
}
