<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Đánh giá kết quả trò chơi lật thẻ Memo Game của bé */
class MemoRating extends Model
{
    use HasFactory;

    protected $table = 'memo_ratings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        /** ID của bé tham gia chơi */
        'child_id',
        /** ID chủ đề thẻ bài */
        'memo_theme_id',
        /** ID cấu hình độ tuổi và kích thước lưới */
        'memo_age_config_id',
        /** Tuổi của bé tại thời điểm làm bài đánh giá */
        'age',
        /** Tổng thời gian bé đã dùng để hoàn thành (giây) */
        'total_duration_spent',
        /** Tổng số cặp thẻ ghép đúng */
        'total_pairs_matched',
        /** Tổng số lần lật sai */
        'total_mistakes',
        /** Điểm số tổng hợp (thang điểm 100) */
        'score',
        /** Nhãn xếp loại đánh giá (Xuất sắc, Tốt, Khá, Cần rèn luyện...) */
        'evaluation_label',
        /** Nhận xét, lời khuyên chi tiết cho phụ huynh */
        'feedback',
        /** Trạng thái bài đánh giá (active, draft, deleted) */
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'child_id' => 'integer',
        'memo_theme_id' => 'integer',
        'memo_age_config_id' => 'integer',
        'age' => 'integer',
        'total_duration_spent' => 'integer',
        'total_pairs_matched' => 'integer',
        'total_mistakes' => 'integer',
        'score' => 'double',
    ];

    /**
     * Thông tin bé tham gia bài đánh giá
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id', 'id');
    }

    /**
     * Chủ đề thẻ bài đã chơi
     */
    public function theme(): BelongsTo
    {
        return $this->belongsTo(MemoTheme::class, 'memo_theme_id', 'id');
    }

    /**
     * Cấu hình độ tuổi và kích thước lưới áp dụng
     */
    public function ageConfig(): BelongsTo
    {
        return $this->belongsTo(MemoAgeConfig::class, 'memo_age_config_id', 'id');
    }

    /**
     * Danh sách chi tiết các lượt chơi (rounds) trong bài đánh giá
     */
    public function rounds(): HasMany
    {
        return $this->hasMany(MemoRatingRound::class, 'memo_rating_id', 'id');
    }

    public function personalBest()
    {
        return $this->hasOne(MemoPersonalBest::class, 'memo_rating_id', 'id');
    }
}
