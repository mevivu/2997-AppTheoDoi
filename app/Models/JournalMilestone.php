<?php

namespace App\Models;

use App\Enums\Journal\MilestoneStage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalMilestone extends Model
{
    use HasFactory;

    protected $table = 'journal_milestones';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        /** Giai đoạn phát triển (newborn: sơ sinh, toddler: tập đi, preschool: mẫu giáo) */
        'stage',
        /** Tên cột mốc gợi ý */
        'name',
        /** Đường dẫn tĩnh (định danh duy nhất) */
        'slug',
        /** Biểu tượng cột mốc */
        'icon',
        /** Tuổi bắt đầu gợi ý (tháng) */
        'age_from_month',
        /** Tuổi kết thúc gợi ý (tháng) */
        'age_to_month',
        /** Thứ tự hiển thị */
        'position',
        /** Trạng thái hoạt động */
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'stage' => MilestoneStage::class,
        'age_from_month' => 'integer',
        'age_to_month' => 'integer',
        'position' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Danh sách nhật ký gắn với cột mốc này
     */
    public function journals(): HasMany
    {
        return $this->hasMany(Journal::class, 'milestone_id');
    }
}
