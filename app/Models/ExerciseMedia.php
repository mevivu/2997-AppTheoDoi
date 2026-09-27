<?php

namespace App\Models;

use App\Enums\Exercise\ExerciseMediaType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Mô hình ExerciseMedia
 *
 * Quản lý các tệp media minh họa (hình ảnh, video hướng dẫn) đính kèm theo từng bài tập.
 */
class ExerciseMedia extends Model
{
    use HasFactory;

    protected $table = 'exercise_media';

    protected $fillable = [
        /** ID bài tập liên kết */
        'exercise_id',
        /** Tiêu đề video con */
        'title',
        /** Loại media: image (Hình ảnh), video (Video) */
        'media_type',
        /** Nguồn video: youtube, r2 */
        'video_type',
        /** Đường dẫn tệp media được lưu trữ hoặc URL YouTube / Cloudflare R2 */
        'media_file',
        /** Đường dẫn ảnh thumbnail đại diện (cho video) */
        'thumbnail',
        /** Thứ tự sắp xếp hiển thị */
        'sort_order',
    ];

    protected $casts = [
        'exercise_id' => 'integer',
        'media_type' => ExerciseMediaType::class,
        'sort_order' => 'integer',
    ];

    protected $appends = ['media_file_url', 'thumbnail_url'];

    /**
     * Đường dẫn URL đầy đủ của tệp media
     */
    public function getMediaFileUrlAttribute(): ?string
    {
        if (!$this->media_file) {
            return null;
        }
        if (str_starts_with($this->media_file, 'http://') || str_starts_with($this->media_file, 'https://')) {
            return $this->media_file;
        }
        return asset($this->media_file);
    }

    /**
     * Đường dẫn URL đầy đủ của ảnh thumbnail
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->thumbnail) {
            return null;
        }
        if (str_starts_with($this->thumbnail, 'http://') || str_starts_with($this->thumbnail, 'https://')) {
            return $this->thumbnail;
        }
        return asset($this->thumbnail);
    }

    /**
     * Bài tập liên kết chứa tệp media này
     */
    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }
}
