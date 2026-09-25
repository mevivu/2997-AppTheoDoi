<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Kỷ lục thành tích cá nhân (Personal Best) của trẻ trong trò chơi Memo Game
 * Ghi nhận thời gian thắng ít nhất (giây) và số lần lật ít nhất theo từng cấu hình lưới (level) và chủ đề
 */
class MemoPersonalBest extends Model
{
    use HasFactory;

    protected $table = 'memo_personal_bests';

    /**
     * Danh sách các trường được phép gán dữ liệu hàng loạt
     */
    protected $fillable = [
        /** ID hồ sơ của bé */
        'child_id',
        /** ID cấu hình lưới/độ tuổi (VD: 2x2, 2x3, 3x4, 4x5, 5x6... càng nhiều thẻ độ khó càng cao) */
        'memo_age_config_id',
        /** ID chủ đề thẻ bài (null = Kỷ lục chung tốt nhất mọi chủ đề ở level này) */
        'memo_theme_id',
        /** Kỷ lục thời gian thắng ván ít nhất (giây) */
        'best_time_seconds',
        /** Kỷ lục số lần lật thẻ ít nhất trong một ván thắng */
        'best_moves',
        /** Số lần lật sai ít nhất trong một ván thắng */
        'min_mistakes',
        /** Tổng số ván bé đã chiến thắng ở cấp độ/chủ đề này */
        'total_wins',
        /** Thời điểm gần nhất bé chơi ván đạt kỷ lục hoặc chiến thắng */
        'last_played_at',
    ];

    /**
     * Ép kiểu dữ liệu
     */
    protected $casts = [
        'child_id' => 'integer',
        'memo_age_config_id' => 'integer',
        'memo_theme_id' => 'integer',
        'best_time_seconds' => 'integer',
        'best_moves' => 'integer',
        'min_mistakes' => 'integer',
        'total_wins' => 'integer',
        'last_played_at' => 'datetime',
    ];

    /**
     * Hồ sơ bé sở hữu kỷ lục
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id', 'id');
    }

    /**
     * Cấu hình cấp độ / độ tuổi tương ứng
     */
    public function ageConfig(): BelongsTo
    {
        return $this->belongsTo(MemoAgeConfig::class, 'memo_age_config_id', 'id');
    }

    /**
     * Chủ đề thẻ bài tương ứng
     */
    public function theme(): BelongsTo
    {
        return $this->belongsTo(MemoTheme::class, 'memo_theme_id', 'id');
    }
}
