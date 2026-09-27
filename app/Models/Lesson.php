<?php

namespace App\Models;

use App\Admin\Support\Eloquent\Sluggable;
use App\Enums\ActiveStatus;
use App\Enums\Lesson\LessonAccessType;
use App\Enums\Lesson\LessonDifficulty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Mô hình Lesson (Bài học giáo dục)
 *
 * Quản lý bài học giáo dục thuộc các danh mục con, có thể gắn 2 hoặc nhiều video (YouTube / Cloudflare R2).
 */
class Lesson extends Model
{
    use HasFactory, Sluggable;

    protected $table = 'lessons';

    protected $fillable = [
        'lesson_category_id',
        'name',
        'slug',
        'image',
        'description',
        'content',
        'difficulty',
        'frequency',
        'benefit',
        'tools',
        'access_type',
        'view_count',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'lesson_category_id' => 'integer',
        'difficulty' => LessonDifficulty::class,
        'access_type' => LessonAccessType::class,
        'view_count' => 'integer',
        'sort_order' => 'integer',
        'status' => ActiveStatus::class,
    ];

    protected $appends = ['video_count', 'first_video_thumbnail', 'thumbnail_url'];

    /**
     * Danh mục con bài học
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(LessonCategory::class, 'lesson_category_id');
    }

    /**
     * Video của bài học (mỗi bài học có 1 video)
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

    public function getVideoCountAttribute(): int
    {
        return $this->videos()->count();
    }

    public function getThumbnailUrlAttribute(): string
    {
        if (!empty($this->image)) {
            return asset($this->image);
        }
        $first = $this->videos->first();
        if ($first) {
            return $first->thumbnail_url;
        }
        return asset('assets/images/default.png');
    }

    public function getFirstVideoThumbnailAttribute(): string
    {
        $first = $this->videos->first();
        if ($first) {
            return $first->thumbnail_url;
        }
        return asset('assets/images/default.png');
    }

    public function isFree(): bool
    {
        return ($this->access_type ?? LessonAccessType::Free) === LessonAccessType::Free;
    }

    public function isVip(): bool
    {
        return ($this->access_type ?? null) === LessonAccessType::Vip;
    }

    public function scopeActive($query)
    {
        return $query->where('status', ActiveStatus::Active->value);
    }

    public function scopeFree($query)
    {
        return $query->where('access_type', LessonAccessType::Free->value);
    }

    public function scopeVip($query)
    {
        return $query->where('access_type', LessonAccessType::Vip->value);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('lesson_category_id', $categoryId);
    }

    public function scopeByDifficulty($query, LessonDifficulty|string $difficulty)
    {
        $val = $difficulty instanceof LessonDifficulty ? $difficulty->value : $difficulty;
        return $query->where('difficulty', $val);
    }
}
