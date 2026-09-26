<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Mô hình AgeGroup
 *
 * Quản lý các nhóm độ tuổi phục vụ phân loại danh mục video giáo dục và bài tập cho trẻ.
 */
class AgeGroup extends Model
{
    use HasFactory;

    protected $table = 'age_groups';

    protected $fillable = [
        /** Tên nhóm tuổi (ví dụ: Thai giáo, 0-2 tuổi...) */
        'name',
        /** Độ tuổi tối thiểu theo tháng (null nếu là Thai giáo) */
        'min_months',
        /** Độ tuổi tối đa theo tháng (null nếu là Thai giáo hoặc không giới hạn) */
        'max_months',
        /** Thứ tự sắp xếp hiển thị */
        'sort_order',
        /** Trạng thái hoạt động */
        'status',
    ];

    protected $casts = [
        'min_months' => 'integer',
        'max_months' => 'integer',
        'sort_order' => 'integer',
        'status' => ActiveStatus::class,
    ];

    /**
     * Danh sách các danh mục video thuộc nhóm tuổi này
     */
    public function videoCategories(): HasMany
    {
        return $this->hasMany(VideoCategory::class, 'age_group_id');
    }

    /**
     * Danh sách các danh mục bài tập thuộc nhóm tuổi này
     */
    public function exerciseCategories(): HasMany
    {
        return $this->hasMany(ExerciseCategory::class, 'age_group_id');
    }

    /**
     * Phạm vi truy vấn các nhóm tuổi đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('status', ActiveStatus::Active);
    }

    /**
     * Phạm vi truy vấn lấy 3 nhóm tuổi gần nhất với độ tuổi hiện tại của trẻ (tính theo tháng)
     */
    public function scopeNearestToChild($query, int $childAgeMonths)
    {
        return $query->where('status', ActiveStatus::Active)
            ->where(function ($q) {
                $q->whereNotNull('min_months')->orWhereNotNull('max_months');
            })
            ->orderByRaw(
                "ABS((COALESCE(min_months, 0) + COALESCE(max_months, min_months, 0)) / 2 - ?)",
                [$childAgeMonths]
            )
            ->take(3);
    }
}
