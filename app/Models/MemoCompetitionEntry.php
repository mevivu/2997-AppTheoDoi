<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Lượt tham gia thi đấu của trẻ trong giải đấu Memo Game
 * Ghi nhận toàn bộ thành tích 1 lượt thi gồm 4 ván liên tục (5x6, không xem trước)
 */
class MemoCompetitionEntry extends Model
{
    use HasFactory;

    protected $table = 'memo_competition_entries';

    /**
     * Danh sách các trường được phép gán dữ liệu hàng loạt
     */
    protected $fillable = [
        /** ID giải đấu Memo Game */
        'memo_competition_id',
        /** ID hồ sơ của bé tham gia thi đấu */
        'child_id',
        /** Lần thi thứ mấy của bé trong giải đấu này (1, 2, 3...) */
        'attempt_number',
        /** Tổng thời gian hoàn thành cả 4 ván (giây) - Tiêu chí xếp hạng chính #1 */
        'total_time',
        /** Tổng số lượt lật thẻ bài qua 4 ván - Tiêu chí phụ xếp hạng #2 khi thời gian bằng nhau */
        'total_moves',
        /** Tổng số lần lật sai cặp thẻ qua 4 ván */
        'total_mistakes',
        /** Tổng số cặp thẻ đã ghép đúng qua 4 ván */
        'total_pairs_matched',
        /** Số ván thi đã thắng trong lượt này (tối đa 4) */
        'games_won',
        /** Trạng thái lượt thi: 'in_progress', 'completed', 'abandoned', 'disqualified' */
        'status',
        /** Trạng thái lượt thi hợp lệ (thắng toàn bộ 4/4 ván và không bỏ cuộc giữa chừng) */
        'is_valid',
        /** Thứ hạng chung cuộc trong giải đấu (1: Quán quân, 2: Á quân...) */
        'ranking',
        /** Thời gian bắt đầu lượt thi */
        'started_at',
        /** Thời gian kết thúc toàn bộ lượt thi */
        'completed_at',
    ];

    /**
     * Ép kiểu dữ liệu cho các trường khi truy vấn
     */
    protected $casts = [
        'memo_competition_id' => 'integer',
        'child_id' => 'integer',
        'attempt_number' => 'integer',
        'total_time' => 'integer',
        'total_moves' => 'integer',
        'total_mistakes' => 'integer',
        'total_pairs_matched' => 'integer',
        'games_won' => 'integer',
        'is_valid' => 'boolean',
        'ranking' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Giải đấu tương ứng
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(MemoCompetition::class, 'memo_competition_id', 'id');
    }

    /**
     * Hồ sơ bé tham gia thi
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id', 'id');
    }

    /**
     * Kết quả chi tiết của từng ván thi trong lượt này
     */
    public function rounds(): HasMany
    {
        return $this->hasMany(MemoCompetitionRound::class, 'memo_competition_entry_id', 'id')->orderBy('game_number', 'asc');
    }

    /**
     * Scope lọc các lượt thi đấu hoàn thành hợp lệ (thắng cả 4 ván)
     */
    public function scopeCompletedWins(Builder $query): Builder
    {
        return $query->where('status', 'completed')->where('is_valid', true);
    }
}
