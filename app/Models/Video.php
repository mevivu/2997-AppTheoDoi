<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\Video\VideoAccessType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Mô hình Video
 *
 * Quản lý thông tin video giáo dục liên kết YouTube, phân quyền Free/VIP, xem thử và nhóm tuổi tương ứng.
 */
class Video extends Model
{
    use HasFactory;

    protected $table = 'videos';

    protected $fillable = [
        /** ID danh mục video */
        'video_category_id',
        /** Tiêu đề video */
        'title',
        /** Mô tả chi tiết nội dung video */
        'description',
        /** Đường dẫn video YouTube */
        'video_url',
        /** Ảnh đại diện (tùy chọn tải lên thủ công) */
        'thumbnail',
        /** Thời lượng video tính theo giây */
        'duration_seconds',
        /** Phân quyền truy cập: free (Miễn phí), vip (Gói VIP) */
        'access_type',
        /** Cho phép xem thử đối với tài khoản Free (áp dụng khi access_type là VIP) */
        'is_preview',
        /** Thứ tự sắp xếp hiển thị */
        'sort_order',
        /** Lượt xem video */
        'view_count',
        /** Trạng thái hoạt động */
        'status',
    ];

    protected $casts = [
        'video_category_id' => 'integer',
        'duration_seconds' => 'integer',
        'access_type' => VideoAccessType::class,
        'is_preview' => 'boolean',
        'sort_order' => 'integer',
        'view_count' => 'integer',
        'status' => ActiveStatus::class,
    ];

    protected $appends = ['youtube_id', 'thumbnail_url', 'formatted_duration'];

    /**
     * Trích xuất YouTube Video ID từ URL
     * Hỗ trợ: youtube.com/watch?v=X, youtu.be/X, youtube.com/embed/X, youtube.com/shorts/X
     */
    public function getYoutubeIdAttribute(): ?string
    {
        return self::extractYouTubeId($this->video_url);
    }

    /**
     * Trích xuất YouTube Video ID từ bất kỳ URL YouTube nào
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
     * Tự động lấy thời lượng video từ YouTube (tính bằng giây)
     */
    public static function extractYouTubeDuration(?string $url): ?int
    {
        $id = self::extractYouTubeId($url);
        if (!$id) {
            return null;
        }

        try {
            $context = stream_context_create([
                'http' => [
                    'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36\r\nAccept-Language: vi,en-US;q=0.9,en;q=0.8\r\n",
                    'timeout' => 4,
                ]
            ]);
            $html = @file_get_contents("https://www.youtube.com/watch?v={$id}", false, $context);
            if ($html && preg_match('/"approxDurationMs":"(\d+)"/', $html, $matches)) {
                return (int) round($matches[1] / 1000);
            }
            if ($html && preg_match('/"lengthSeconds":"(\d+)"/', $html, $matches)) {
                return (int) $matches[1];
            }
        } catch (\Throwable $e) {
            // fallback silently
        }

        return null;
    }

    /**
     * Thời lượng định dạng dễ đọc (ví dụ: 03:33 hoặc 01:15:20)
     */
    public function getFormattedDurationAttribute(): string
    {
        $sec = (int) $this->duration_seconds;
        if ($sec <= 0) {
            return '—';
        }
        $m = floor($sec / 60);
        $s = $sec % 60;
        if ($m >= 60) {
            $h = floor($m / 60);
            $m = $m % 60;
            return sprintf('%02d:%02d:%02d', $h, $m, $s);
        }
        return sprintf('%02d:%02d', $m, $s);
    }

    /**
     * Đường dẫn ảnh đại diện: ưu tiên ảnh tùy chọn tải lên, fallback ảnh tự động từ YouTube
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->thumbnail) {
            return asset($this->thumbnail);
        }
        if ($this->youtube_id) {
            return "https://img.youtube.com/vi/{$this->youtube_id}/hqdefault.jpg";
        }
        return null;
    }

    /**
     * Danh mục video liên kết
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(VideoCategory::class, 'video_category_id');
    }

    /**
     * Phạm vi truy vấn video đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('status', ActiveStatus::Active);
    }

    /**
     * Phạm vi truy vấn video miễn phí
     */
    public function scopeFree($query)
    {
        return $query->where('access_type', VideoAccessType::FREE);
    }

    /**
     * Phạm vi truy vấn video thuộc gói VIP
     */
    public function scopeVip($query)
    {
        return $query->where('access_type', VideoAccessType::VIP);
    }

    /**
     * Phạm vi truy vấn video được cấu hình cho phép xem thử
     */
    public function scopePreview($query)
    {
        return $query->where('is_preview', true);
    }
}
