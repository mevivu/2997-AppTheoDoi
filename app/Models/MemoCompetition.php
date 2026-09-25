<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Giải đấu Memo Game
 * Quản lý các cuộc thi do admin phát động cho trẻ thi đấu liên hoàn 4 ván, lưới 5×6, không cho xem trước
 */
class MemoCompetition extends Model
{
    use HasFactory;

    protected $table = 'memo_competitions';

    /**
     * Danh sách các trường được phép gán dữ liệu hàng loạt (mass assignable)
     */
    protected $fillable = [
        /** Tên giải đấu (VD: Giải Vô Địch Trí Nhớ Nhí Tuần 1) */
        'name',
        /** Đường dẫn thân thiện URL (slug) */
        'slug',
        /** Mô tả chi tiết, thể lệ và phần thưởng giải đấu */
        'description',
        /** Đường dẫn hình ảnh banner giải đấu */
        'banner_image',
        /** Thời gian bắt đầu mở giải đấu */
        'start_at',
        /** Thời gian kết thúc giải đấu */
        'end_at',
        /** ID cấu hình lưới/độ tuổi áp dụng cho giải đấu (chuẩn 5×6) */
        'memo_age_config_id',
        /** Tổng số ván game phải chơi liên tục trong 1 lượt thi (mặc định: 4 ván) */
        'total_games',
        /** Thời gian xem trước thẻ bài ghi đè cho giải (0 giây = không cho xem trước để chống gian lận) */
        'peek_time_override',
        /** Số lượt thi tối đa cho mỗi bé (1: chỉ thi 1 lần duy nhất, >1: cho phép thi lại N lần, 0: không giới hạn) */
        'max_attempts',
        /** Quy định bắt buộc phải thắng toàn bộ các ván thi mới được tính kết quả xếp hạng (mặc định: true) */
        'must_win_all',
        /** Thời điểm hệ thống chốt tính toán thứ hạng chung cuộc khi giải kết thúc */
        'ranking_calculated_at',
        /** Trạng thái giải đấu: 'draft' (nháp), 'upcoming' (sắp diễn ra), 'active' (đang mở), 'ended' (đã kết thúc), 'cancelled' (đã hủy) */
        'status',
        /** ID quản trị viên tạo giải đấu */
        'created_by',
    ];

    /**
     * Ép kiểu dữ liệu cho các trường khi truy vấn
     */
    protected $casts = [
        'memo_age_config_id' => 'integer',
        'total_games' => 'integer',
        'peek_time_override' => 'integer',
        'max_attempts' => 'integer',
        'must_win_all' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'ranking_calculated_at' => 'datetime',
        'created_by' => 'integer',
    ];

    /**
     * Cấu hình độ tuổi & kích thước lưới áp dụng cho giải đấu
     */
    public function ageConfig(): BelongsTo
    {
        return $this->belongsTo(MemoAgeConfig::class, 'memo_age_config_id', 'id');
    }

    /**
     * Danh sách 4 chủ đề và thứ tự ván thi trong giải đấu
     */
    public function competitionThemes(): HasMany
    {
        return $this->hasMany(MemoCompetitionTheme::class, 'memo_competition_id', 'id')->orderBy('game_order', 'asc');
    }

    /**
     * Quan hệ Many-to-Many lấy danh sách chủ đề qua bảng trung gian memo_competition_themes
     */
    public function themes(): BelongsToMany
    {
        return $this->belongsToMany(MemoTheme::class, 'memo_competition_themes', 'memo_competition_id', 'memo_theme_id')
            ->withPivot('game_order')
            ->withTimestamps()
            ->orderByPivot('game_order', 'asc');
    }

    /**
     * Danh sách các lượt thi đấu của các bé trong giải đấu này
     */
    public function entries(): HasMany
    {
        return $this->hasMany(MemoCompetitionEntry::class, 'memo_competition_id', 'id');
    }

    /**
     * Quản trị viên khởi tạo giải đấu
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by', 'id');
    }

    /**
     * Scope lọc các giải đấu đang diễn ra trong khung thời gian hợp lệ
     */
    public function scopeActive(Builder $query): Builder
    {
        $now = Carbon::now();
        return $query->where('status', 'active')
            ->where('start_at', '<=', $now)
            ->where('end_at', '>=', $now);
    }

    /**
     * Scope lọc các giải đấu sắp diễn ra
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        $now = Carbon::now();
        return $query->where(function ($q) use ($now) {
            $q->where('status', 'upcoming')
              ->orWhere(function ($q2) use ($now) {
                  $q2->where('status', 'active')->where('start_at', '>', $now);
              });
        });
    }

    /**
     * Scope lọc các giải đấu đã kết thúc
     */
    public function scopeEnded(Builder $query): Builder
    {
        $now = Carbon::now();
        return $query->where('status', 'ended')
            ->orWhere(function ($q) use ($now) {
                $q->where('status', 'active')->where('end_at', '<', $now);
            });
    }

    /**
     * Kiểm tra giải đấu hiện tại có đang mở và trong thời gian thi đấu không
     */
    public function isHappening(): bool
    {
        $now = Carbon::now();
        return $this->status === 'active' && $this->start_at <= $now && $this->end_at >= $now;
    }

    /**
     * Kiểm tra giải đấu đã hết thời gian thi đấu hay chưa
     */
    public function hasEnded(): bool
    {
        return $this->status === 'ended' || ($this->end_at && $this->end_at < Carbon::now());
    }

    /**
     * Alias cho isHappening
     */
    public function isOpen(): bool
    {
        return $this->isHappening();
    }

    /**
     * Alias cho hasEnded
     */
    public function isEnded(): bool
    {
        return $this->hasEnded();
    }
}
