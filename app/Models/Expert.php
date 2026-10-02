<?php

namespace App\Models;

use App\Enums\DefaultStatus;
use App\Enums\Expert\ExpertCouncilType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expert extends Model
{
    use HasFactory;

    protected $table = 'experts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        /** Phân loại hội đồng cố vấn (1: Hội đồng Cố vấn Chiến lược, 2: Hội đồng Tư vấn Chuyên môn) */
        'council_type',
        /** Họ và tên chuyên gia / Bác sĩ */
        'name',
        /** Chức danh / Học vị / Vị trí đảm nhiệm (ThS, TS, BS. CKII, Giảng viên, Chủ tịch...) */
        'title',
        /** Bệnh viện / Viện nghiên cứu công tác */
        'hospital',
        /** Đơn vị / Cơ quan / Doanh nghiệp công tác */
        'workplace',
        /** Ảnh chân dung đại diện / Avatar */
        'avatar',
        /** Tiểu sử tóm tắt / Lĩnh vực chuyên môn / Bằng cấp */
        'bio',
        /** Đường dẫn liên kết tư vấn / Website / Zalo ("Nút Liên hệ" trên App) */
        'contact_link',
        /** Số điện thoại liên hệ tư vấn */
        'contact_phone',
        /** Trạng thái xác minh chuyên gia uy tín */
        'is_verified',
        /** Thứ tự sắp xếp ưu tiên hiển thị */
        'sort_order',
        /** Trạng thái hoạt động (1: Hiển thị, 2: Bản nháp, 3: Thùng rác) */
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'council_type' => ExpertCouncilType::class,
        'is_verified' => 'boolean',
        'status' => DefaultStatus::class,
        'sort_order' => 'integer',
    ];

    /**
     * Danh sách bài viết chuyên môn do chuyên gia biên soạn
     */
    public function expertPosts(): HasMany
    {
        return $this->hasMany(ExpertPost::class, 'expert_id');
    }

    /**
     * Danh sách học vị & bằng cấp chuyên môn của chuyên gia
     */
    public function qualifications(): HasMany
    {
        return $this->hasMany(ExpertQualification::class, 'expert_id')
            ->where('status', DefaultStatus::Published)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * Scope lọc chuyên gia đã kích hoạt xuất bản
     */
    public function scopePublished($query)
    {
        return $query->where('status', DefaultStatus::Published);
    }

    /**
     * Scope lọc thành viên Hội đồng Cố vấn Chiến lược
     */
    public function scopeStrategic($query)
    {
        return $query->where('council_type', ExpertCouncilType::Strategic);
    }

    /**
     * Scope lọc thành viên Hội đồng Tư vấn Chuyên môn
     */
    public function scopeProfessional($query)
    {
        return $query->where('council_type', ExpertCouncilType::Professional);
    }

    /**
     * Kiểm tra chuyên gia đang ở trạng thái hiển thị
     */
    public function isPublished(): bool
    {
        return $this->status === DefaultStatus::Published;
    }
}
