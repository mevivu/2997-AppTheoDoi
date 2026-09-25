<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Chi tiết kết quả từng ván thi trong lượt thi giải đấu Memo Game
 * Ghi nhận thời gian, số lần lật và kết quả thắng/thua của từng ván
 */
class MemoCompetitionRound extends Model
{
    use HasFactory;

    protected $table = 'memo_competition_rounds';

    /**
     * Danh sách các trường được phép gán dữ liệu hàng loạt
     */
    protected $fillable = [
        /** ID lượt thi tương ứng */
        'memo_competition_entry_id',
        /** ID chủ đề thẻ bài của ván thi này */
        'memo_theme_id',
        /** Thứ tự ván thi trong lượt (1 -> 4) */
        'game_number',
        /** Thời gian hoàn thành ván chơi (giây) */
        'duration_spent',
        /** Số cặp thẻ bài ghép đúng trong ván */
        'pairs_matched',
        /** Tổng số lần lật thẻ trong ván */
        'total_moves',
        /** Số lần lật sai cặp thẻ trong ván */
        'mistakes',
        /** Kết quả ván thi (true: thắng/ghép hết cặp, false: thua/hết giờ) */
        'is_won',
        /** Thời điểm bắt đầu ván thi */
        'started_at',
        /** Thời điểm hoàn thành ván thi */
        'completed_at',
    ];

    /**
     * Ép kiểu dữ liệu
     */
    protected $casts = [
        'memo_competition_entry_id' => 'integer',
        'memo_theme_id' => 'integer',
        'game_number' => 'integer',
        'duration_spent' => 'integer',
        'pairs_matched' => 'integer',
        'total_moves' => 'integer',
        'mistakes' => 'integer',
        'is_won' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Lượt thi chứa ván này
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(MemoCompetitionEntry::class, 'memo_competition_entry_id', 'id');
    }

    /**
     * Chủ đề thẻ bài của ván thi
     */
    public function theme(): BelongsTo
    {
        return $this->belongsTo(MemoTheme::class, 'memo_theme_id', 'id');
    }
}
