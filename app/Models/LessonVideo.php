<?php

namespace App\Models;

use App\Enums\Video\VideoType;
use App\Traits\ImageSystem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonVideo extends Model
{
    use HasFactory;

    protected $table = 'lesson_videos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        /** ID bài học */
        'lesson_id',
        /** Tiêu đề video */
        'title',
        /** Loại video (youtube, r2) */
        'video_type',
        /** Đường dẫn video */
        'video_url',
        /** Đường dẫn file trên R2 */
        'video_path',
        /** Ảnh bìa video */
        'thumbnail',
        /** Thời lượng video (giây) */
        'duration_seconds',
        /** Thứ tự sắp xếp */
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'lesson_id' => 'integer',
        'video_type' => VideoType::class,
        'duration_seconds' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['youtube_id', 'thumbnail_url', 'formatted_duration'];

    /**
     * Bài học chứa video này
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    /**
     * Kiểm tra video nguồn YouTube
     */
    public function isYouTube(): bool
    {
        return ($this->video_type ?? VideoType::YouTube) === VideoType::YouTube;
    }

    /**
     * Kiểm tra video nguồn Cloudflare R2
     */
    public function isR2(): bool
    {
        return ($this->video_type ?? null) === VideoType::R2;
    }

    /**
     * Lấy YouTube Video ID
     */
    public function getYoutubeIdAttribute(): ?string
    {
        if ($this->isR2()) {
            return null;
        }
        return self::extractYouTubeId($this->video_url);
    }

    /**
     * Tự động trích xuất YouTube ID từ đường dẫn
     */
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

    /**
     * Ảnh bìa video
     */
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

        return asset(ImageSystem::DEFAULT_IMAGE);
    }

    /**
     * Thời lượng định dạng mm:ss hoặc hh:mm:ss
     */
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

    /**
     * Đường dẫn phát trực tiếp video
     */
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
