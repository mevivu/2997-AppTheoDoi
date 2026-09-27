<?php

namespace App\Models;

use App\Admin\Support\Eloquent\Sluggable;
use App\Enums\ActiveStatus;
use App\Enums\Lesson\EducationPillar;
use App\Enums\Lesson\LessonCategoryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Mô hình LessonCategory
 *
 * Quản lý danh mục bài học phân theo nhóm độ tuổi và 5 trụ cột giáo dục (PQ, IQ, EQ, AQ, Thai giáo).
 */
class LessonCategory extends Model
{
    use HasFactory, Sluggable;

    protected $table = 'lesson_categories';

    protected $fillable = [
        'age_group_id',
        'pillar',
        'key',
        'name',
        'slug',
        'icon',
        'description',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'age_group_id' => 'integer',
        'pillar' => EducationPillar::class,
        'key' => LessonCategoryKey::class,
        'sort_order' => 'integer',
        'status' => ActiveStatus::class,
    ];

    /**
     * Nhóm tuổi tương ứng của danh mục
     */
    public function ageGroup(): BelongsTo
    {
        return $this->belongsTo(AgeGroup::class, 'age_group_id');
    }

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
     * Scope lọc danh mục đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('status', ActiveStatus::Active->value);
    }

    /**
     * Scope lọc theo nhóm tuổi
     */
    public function scopeByAgeGroup($query, $ageGroupId)
    {
        return $query->where('age_group_id', $ageGroupId);
    }

    /**
     * Scope lọc theo trụ cột giáo dục
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
}
