<?php

namespace App\Models;

use App\Admin\Support\Eloquent\Sluggable;
use App\Enums\ActiveStatus;
use App\Enums\Lesson\EducationPillar;
use App\Enums\Lesson\LessonCategoryKey;
use App\Traits\ImageSystem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class LessonCategory extends Model
{
    use HasFactory, Sluggable;

    protected $table = 'lesson_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        /** Trụ cột / Lĩnh vực giáo dục */
        'pillar',
        /** Định danh chuyên môn danh mục */
        'key',
        /** Tên danh mục */
        'name',
        /** Đường dẫn tĩnh */
        'slug',
        /** Biểu tượng icon */
        'icon',
        /** Mô tả danh mục */
        'description',
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
        'pillar' => EducationPillar::class,
        'key' => LessonCategoryKey::class,
        'sort_order' => 'integer',
        'status' => ActiveStatus::class,
    ];

    /**
     * Danh sách bài học thuộc danh mục này
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class, 'lesson_category_id')->orderBy('sort_order', 'asc');
    }

    /**
     * Danh sách tất cả video thông qua các bài học
     */
    public function videos(): HasManyThrough
    {
        return $this->hasManyThrough(LessonVideo::class, Lesson::class, 'lesson_category_id', 'lesson_id');
    }

    /**
     * Lọc danh mục đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('status', ActiveStatus::Active->value);
    }

    /**
     * Lọc theo trụ cột giáo dục
     */
    public function scopeByPillar($query, EducationPillar|string $pillar)
    {
        $val = $pillar instanceof EducationPillar ? $pillar->value : $pillar;
        return $query->where('pillar', $val);
    }

    /**
     * Tên nhãn trụ cột giáo dục
     */
    public function getPillarLabelAttribute(): string
    {
        return $this->pillar?->label() ?? (string) $this->attributes['pillar'];
    }

    /**
     * Tên nhãn chuyên môn của danh mục
     */
    public function getKeyLabelAttribute(): string
    {
        return $this->key?->label() ?? (string) $this->attributes['key'];
    }

    /**
     * Đường dẫn ảnh đại diện danh mục
     */
    public function getIconUrlAttribute(): string
    {
        if (!empty($this->icon)) {
            if (str_starts_with($this->icon, 'http://') || str_starts_with($this->icon, 'https://')) {
                return $this->icon;
            }
            return asset($this->icon);
        }

        return asset(ImageSystem::DEFAULT_IMAGE);
    }
}
