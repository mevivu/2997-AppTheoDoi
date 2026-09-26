<?php

namespace App\Models;

use App\Admin\Support\Eloquent\Sluggable;
use App\Enums\ActiveStatus;
use App\Enums\Exercise\ExerciseTopic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Mô hình ExerciseCategory
 *
 * Quản lý danh mục phân loại bài tập giáo dục theo nhóm độ tuổi và các chủ đề lớn (PQ, IQ, EQ, AQ, Thai giáo).
 */
class ExerciseCategory extends Model
{
    use HasFactory, Sluggable;

    protected $table = 'exercise_categories';

    protected $fillable = [
        /** ID nhóm độ tuổi */
        'age_group_id',
        /** Chủ đề lớn: pq, iq, eq, aq, thai_giao */
        'topic',
        /** ID danh mục cha (nếu là danh mục con/sub-category) */
        'parent_id',
        /** Tên danh mục bài tập */
        'name',
        /** Đường dẫn tĩnh (Slug) */
        'slug',
        /** Icon hoặc hình ảnh đại diện danh mục */
        'icon',
        /** Thứ tự sắp xếp hiển thị */
        'sort_order',
        /** Trạng thái hoạt động */
        'status',
    ];

    protected $casts = [
        'age_group_id' => 'integer',
        'topic' => ExerciseTopic::class,
        'parent_id' => 'integer',
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
     * Danh mục cha trực tiếp
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ExerciseCategory::class, 'parent_id');
    }

    /**
     * Danh sách các danh mục con (Sub-categories)
     */
    public function children(): HasMany
    {
        return $this->hasMany(ExerciseCategory::class, 'parent_id');
    }

    /**
     * Danh sách các bài tập thuộc danh mục này
     */
    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class, 'exercise_category_id');
    }

    /**
     * Phạm vi truy vấn danh mục đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('status', ActiveStatus::Active);
    }

    /**
     * Phạm vi truy vấn danh mục cấp gốc (không có danh mục cha)
     */
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Phạm vi truy vấn theo chủ đề lớn (PQ, IQ, EQ, AQ, Thai giáo)
     */
    public function scopeTopic($query, $topic)
    {
        return $query->where('topic', $topic);
    }
}
