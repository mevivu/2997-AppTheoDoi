<?php

namespace App\Models;

use App\Enums\Video\VideoType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Mô hình LessonVideo
 *
 * Quản lý các video trong bài học (1 bài học có thể có nhiều video từ YouTube hoặc Cloudflare R2).
 */
class LessonVideo extends Model
{
    use HasFactory;

    protected $table = 'lesson_videos';

    protected $fillable = [
        'lesson_id',
        'title',
        'video_type',
        'video_url',
        'video_path',
        'thumbnail',
        'duration_seconds',
        'sort_order',
    ];

    protected $casts = [
        'lesson_id' => 'integer',
        'video_type' => VideoType::class,
        'duration_seconds' => 'integer',
        'sort_order' => 'integer',
    ];

    protected $appends = ['youtube_id', 'thumbnail_url', 'formatted_duration'];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public function isYouTube(): bool
    {
        return ($this->video_type ?? VideoType::YouTube) === VideoType::YouTube;
    }

    public function isR2(): bool
    {
        return ($this->video_type ?? null) === VideoType::R2;
    }

    public function getYoutubeIdAttribute(): ?string
    {
        if ($this->isR2()) {
            return null;
        }
        return self::extractYouTubeId($this->video_url);
    }

    public static function extractYouTubeId(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $m)) {
            return $m[1];
        }
        if (preg_match('/[?&]v=([a-zA-Z0-9_-]+)/', $url, $m)) {
            return $m[1];
        }
        if (preg_match('/embed\/([a-zA-Z0-9_-]+)/', $url, $m)) {
            return $m[1];
        }
        if (preg_match('/shorts\/([a-zA-Z0-9_-]+)/', $url, $m)) {
            return $m[1];
        }

        return null;
    }

    public function getThumbnailUrlAttribute(): string
    {
        if (!empty($this->thumbnail)) {
            if (str_starts_with($this->thumbnail, 'http://') || str_starts_with($this->thumbnail, 'https://')) {
                return $this->thumbnail;
            }
            return asset($this->thumbnail);
        }

        if ($this->isYouTube() && $ytId = $this->youtube_id) {
            return "https://img.youtube.com/vi/{$ytId}/hqdefault.jpg";
        }

        return asset('assets/images/default.png');
    }

    public function getFormattedDurationAttribute(): string
    {
        $sec = (int) ($this->duration_seconds ?? 0);
        if ($sec <= 0) {
            return '00:00';
        }

        $hours = floor($sec / 3600);
        $minutes = floor(($sec % 3600) / 60);
        $seconds = $sec % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function getStreamUrlAttribute(): ?string
    {
        if ($this->isR2()) {
            return $this->video_url;
        }

        if ($this->isYouTube() && $ytId = $this->youtube_id) {
            return "https://www.youtube.com/watch?v={$ytId}";
        }

        return $this->video_url;
    }
}
