<?php

namespace App\Models;

use App\Admin\Support\Eloquent\Sluggable;
use App\Enums\ActiveStatus;
use App\Enums\Lesson\LessonAccessType;
use App\Enums\Lesson\LessonDifficulty;
use App\Traits\ImageSystem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lesson extends Model
{
    use HasFactory, Sluggable;

    protected $table = 'lessons';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        /** ID danh mục bài học */
        'lesson_category_id',
        /** Tên bài học */
        'name',
        /** Đường dẫn tĩnh */
        'slug',
        /** Ảnh đại diện bài học */
        'image',
        /** Tóm tắt bài học */
        'description',
        /** Nội dung hướng dẫn chi tiết cho phụ huynh */
        'content',
        /** Độ khó bài học */
        'difficulty',
        /** Tần suất gợi ý */
        'frequency',
        /** Lợi ích mang lại cho bé */
        'benefit',
        /** Dụng cụ / học cụ cần chuẩn bị */
        'tools',
        /** Phân quyền truy cập (free/vip) */
        'access_type',
        /** Lượt xem bài học */
        'view_count',
        /** Thứ tự sắp xếp */
        'sort_order',
        /** Trạng thái hoạt động */
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'lesson_category_id' => 'integer',
        'difficulty' => LessonDifficulty::class,
        'access_type' => LessonAccessType::class,
        'view_count' => 'integer',
        'sort_order' => 'integer',
        'status' => ActiveStatus::class,
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['video_count', 'first_video_thumbnail', 'thumbnail_url'];

    /**
     * Danh mục của bài học
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(LessonCategory::class, 'lesson_category_id');
    }

    /**
     * Video chính của bài học
     */
    public function video(): HasOne
    {
        return $this->hasOne(LessonVideo::class, 'lesson_id')->orderBy('sort_order', 'asc');
    }

    /**
     * Danh sách video trong bài học
     */
    public function videos(): HasMany
    {
        return $this->hasMany(LessonVideo::class, 'lesson_id')->orderBy('sort_order', 'asc');
    }

    /**
     * Video đầu tiên của bài học
     */
    public function firstVideo(): HasOne
    {
        return $this->hasOne(LessonVideo::class, 'lesson_id')->orderBy('sort_order', 'asc');
    }

    /**
     * Tổng số lượng video trong bài học
     */
    public function getVideoCountAttribute(): int
    {
        return $this->videos()->count();
    }

    /**
     * Đường dẫn ảnh bìa bài học
     */
    public function getThumbnailUrlAttribute(): string
    {
        if (!empty($this->image)) {
            return asset($this->image);
        }
        $first = $this->videos->first();
        if ($first) {
            return $first->thumbnail_url;
        }
        return asset(ImageSystem::DEFAULT_IMAGE);
    }

    /**
     * Ảnh đại diện video đầu tiên
     */
    public function getFirstVideoThumbnailAttribute(): string
    {
        $first = $this->videos->first();
        if ($first) {
            return $first->thumbnail_url;
        }
        return asset(ImageSystem::DEFAULT_IMAGE);
    }

    /**
     * Kiểm tra bài học miễn phí
     */
    public function isFree(): bool
    {
        return ($this->access_type ?? LessonAccessType::Free) === LessonAccessType::Free;
    }

    /**
     * Kiểm tra bài học VIP
     */
    public function isVip(): bool
    {
        return ($this->access_type ?? null) === LessonAccessType::Vip;
    }

    /**
     * Lọc bài học đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('status', ActiveStatus::Active->value);
    }

    /**
     * Lọc bài học miễn phí
     */
    public function scopeFree($query)
    {
        return $query->where('access_type', LessonAccessType::Free->value);
    }

    /**
     * Lọc bài học VIP
     */
    public function scopeVip($query)
    {
        return $query->where('access_type', LessonAccessType::Vip->value);
    }

    /**
     * Lọc theo danh mục
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('lesson_category_id', $categoryId);
    }

    /**
     * Lọc theo độ khó
     */
    public function scopeByDifficulty($query, LessonDifficulty|string $difficulty)
    {
        $val = $difficulty instanceof LessonDifficulty ? $difficulty->value : $difficulty;
        return $query->where('difficulty', $val);
    }
}
