<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Chi tiết từng lượt chơi trong bài kiểm tra Memo Game */
class MemoRatingRound extends Model
{
    use HasFactory;

    protected $table = 'memo_rating_rounds';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        /** ID bài đánh giá tổng thể */
        'memo_rating_id',
        /** Lượt chơi số mấy (1, 2, 3...) */
        'round_number',
        /** Thời gian bé hoàn thành lượt chơi này (giây) */
        'duration_spent',
        /** Số cặp thẻ đã ghép đúng trong lượt này */
        'pairs_matched',
        /** Số lần lật sai trong lượt này */
        'mistakes',
        /** Điểm số đạt được trong lượt này */
        'score',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'memo_rating_id' => 'integer',
        'round_number' => 'integer',
        'duration_spent' => 'integer',
        'pairs_matched' => 'integer',
        'mistakes' => 'integer',
        'score' => 'double',
    ];

    /**
     * Bài đánh giá tổng thể chứa lượt chơi này
     */
    public function rating(): BelongsTo
    {
        return $this->belongsTo(MemoRating::class, 'memo_rating_id', 'id');
    }
}
