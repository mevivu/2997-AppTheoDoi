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
        /** Loại media: image (Hình ảnh), video (Video) */
        'media_type',
        /** Đường dẫn tệp media được lưu trữ */
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
        return $this->media_file ? asset($this->media_file) : null;
    }

    /**
     * Đường dẫn URL đầy đủ của ảnh thumbnail
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail ? asset($this->thumbnail) : null;
    }

    /**
     * Bài tập liên kết chứa tệp media này
     */
    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }
}
