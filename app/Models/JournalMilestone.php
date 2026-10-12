<?php

namespace App\Models;

use App\Enums\Journal\MilestoneStage;
use App\Enums\Permission\PermissionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        /** Phân loại quyền sở hữu (admin: mốc chuẩn hệ thống, user: mốc do phụ huynh tự thêm) */
        'type',
        /** ID của bé sở hữu mốc riêng (nếu là user) */
        'child_id',
        /** ID của phụ huynh tạo mốc (nếu là user) */
        'user_id',
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
        'type' => PermissionType::class,
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

    /**
     * Bé sở hữu cột mốc (đối với mốc người dùng tự thêm)
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id');
    }

    /**
     * Phụ huynh tạo cột mốc (đối với mốc người dùng tự thêm)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope lọc cột mốc chuẩn của hệ thống (do Quản trị viên tạo)
     */
    public function scopeAdmin(Builder $query): Builder
    {
        return $query->where('type', PermissionType::ADMIN);
    }

    /**
     * Scope lọc cột mốc do phụ huynh tự thêm
     */
    public function scopeOfUser(Builder $query): Builder
    {
        return $query->where('type', PermissionType::USER);
    }

    public function scopeUser(Builder $query): Builder
    {
        return $query->where('type', PermissionType::USER);
    }

    /**
     * Scope lấy danh sách cột mốc áp dụng cho một bé (gồm cả mốc hệ thống và mốc riêng của bé)
     */
    public function scopeForChild(Builder $query, int $childId): Builder
    {
        return $query->where(function (Builder $q) use ($childId) {
            $q->where('type', PermissionType::ADMIN)
                ->orWhere(function (Builder $sub) use ($childId) {
                    $sub->where('type', PermissionType::USER)
                        ->where('child_id', $childId);
                });
        });
    }
}

