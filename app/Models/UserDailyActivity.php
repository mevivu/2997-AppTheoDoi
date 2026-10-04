<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Hoạt động Hằng Ngày của Người Dùng (Phụ Huynh)
 *
 * Lưu trữ nhật ký tổng hợp thời gian và số phiên sử dụng app trong ngày
 * phục vụ cho việc tính điểm và phân hạng phụ huynh hàng tháng.
 */
class UserDailyActivity extends Model
{
    use HasFactory;

    protected $table = 'user_daily_activities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        /** ID người dùng (phụ huynh) */
        'user_id',
        /** Ngày ghi nhận hoạt động (YYYY-MM-DD) */
        'activity_date',
        /** Tổng thời gian sử dụng app trong ngày (giây) */
        'active_seconds',
        /** Tổng số phiên mở app trong ngày */
        'session_count',
        /** Thời điểm gửi heartbeat gần nhất trong ngày */
        'last_heartbeat_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'activity_date' => 'date',
        'active_seconds' => 'integer',
        'session_count' => 'integer',
        'last_heartbeat_at' => 'datetime',
    ];

    /**
     * Người dùng (phụ huynh)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
