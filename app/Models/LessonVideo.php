<?php

namespace App\Models;

use App\Enums\Video\VideoType;
use App\Traits\ImageSystem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Mô hình LessonVideo (Video đính kèm bài học)
 *
 * Quản lý các video trong bài học (1 bài học có thể gắn video từ YouTube hoặc Cloudflare R2).
 * Hỗ trợ tự động trích xuất YouTube ID, render ảnh thumbnail và tính toán thời lượng phát.
 */
class LessonVideo extends Model
{
    use HasFactory;

    /**
     * Bảng cơ sở dữ liệu tương ứng
     *
     * @var string
     */
    protected $table = 'lesson_videos';

    /**
     * Các trường dữ liệu cho phép gán hàng loạt (Mass Assignment)
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lesson_id',        // ID bài học
        'title',            // Tiêu đề video (ví dụ: Video 1 - Hướng dẫn, Video 2 - Thực hành)
        'video_type',       // Nguồn phát video: youtube, r2 (Enum VideoType)
        'video_url',        // Đường dẫn liên kết video (YouTube URL hoặc CDN R2)
        'video_path',       // S3/R2 Object Key lưu trữ trên Cloudflare R2
        'thumbnail',        // Ảnh bìa tùy chọn của video
        'duration_seconds', // Thời lượng video tính theo giây
        'sort_order',       // Thứ tự hiển thị
    ];

    /**
     * Ép kiểu dữ liệu tự động cho các thuộc tính
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
     * Các thuộc tính ảo bổ sung vào mảng/JSON khi serialize
     *
     * @var array<int, string>
     */
    protected $appends = ['youtube_id', 'thumbnail_url', 'formatted_duration'];

    /**
     * Liên kết: Bài học mà video này thuộc về
     *
     * @return BelongsTo
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    /**
     * Kiểm tra video có phải nguồn từ YouTube hay không
     *
     * @return bool
     */
    public function isYouTube(): bool
    {
        return ($this->video_type ?? VideoType::YouTube) === VideoType::YouTube;
    }

    /**
     * Kiểm tra video có phải nguồn từ Cloudflare R2 hay không
     *
     * @return bool
     */
    public function isR2(): bool
    {
        return ($this->video_type ?? null) === VideoType::R2;
    }

    /**
     * Thuộc tính ảo: Trích xuất YouTube Video ID từ URL video
     *
     * @return string|null
     */
    public function getYoutubeIdAttribute(): ?string
    {
        if ($this->isR2()) {
            return null;
        }
        return self::extractYouTubeId($this->video_url);
    }

    /**
     * Phương thức tĩnh: Trích xuất YouTube ID từ mọi định dạng liên kết YouTube
     * Hỗ trợ: youtu.be, watch?v=, embed/, shorts/
     *
     * @param string|null $url
     * @return string|null
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
     * Thuộc tính ảo: URL ảnh bìa / thumbnail của video
     * Ưu tiên: Ảnh bìa tùy chỉnh -> Ảnh thumbnail YouTube -> Ảnh mặc định hệ thống (ImageSystem::DEFAULT_IMAGE)
     *
     * @return string
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
     * Thuộc tính ảo: Định dạng thời lượng video sang dạng mm:ss hoặc hh:mm:ss
     *
     * @return string
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
     * Thuộc tính ảo: URL phát trực tiếp (Streaming URL) của video
     *
     * @return string|null
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
