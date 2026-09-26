<?php

namespace App\Models;

use App\Admin\Support\Eloquent\Sluggable;
use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Mô hình VideoCategory
 *
 * Quản lý danh mục phân loại video giáo dục, liên kết với từng nhóm độ tuổi và hỗ trợ danh mục cha - con.
 */
class VideoCategory extends Model
{
    use HasFactory, Sluggable;

    protected $table = 'video_categories';

    protected $fillable = [
        /** ID nhóm độ tuổi */
        'age_group_id',
        /** ID danh mục cha (nếu là danh mục con) */
        'parent_id',
        /** Tên danh mục video */
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
        'parent_id' => 'integer',
        'sort_order' => 'integer',
        'status' => ActiveStatus::class,
    ];

    /**
     * Nhóm tuổi liên kết của danh mục
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
        return $this->belongsTo(VideoCategory::class, 'parent_id');
    }

    /**
     * Danh sách các danh mục con
     */
    public function children(): HasMany
    {
        return $this->hasMany(VideoCategory::class, 'parent_id');
    }

    /**
     * Danh sách các video thuộc danh mục này
     */
    public function videos(): HasMany
    {
        return $this->hasMany(Video::class, 'video_category_id');
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
}
