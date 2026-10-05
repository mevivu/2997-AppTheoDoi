<?php

namespace App\Models;

use App\Enums\User\ParentRank;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Bản Chụp Thứ Hạng Phụ Huynh Theo Kỳ (Tháng)
 *
 * Ghi nhận toàn bộ thông số hoạt động, điểm số thành phần và cấp bậc
 * của phụ huynh trong kỳ (định dạng YYYY-MM).
 */
class ParentRankSnapshot extends Model
{
    use HasFactory;

    protected $table = 'parent_rank_snapshots';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        /** ID người dùng (phụ huynh) */
        'user_id',
        /** Kỳ đánh giá (định dạng YYYY-MM) */
        'period',
        /** Tổng thời gian sử dụng app trong kỳ (phút) */
        'usage_minutes',
        /** Số ngày có mở app trong kỳ */
        'active_days',
        /** Tổng số phiên mở app trong kỳ */
        'session_count',
        /** Tổng số bài đánh giá đã làm cho con trong kỳ */
        'assessment_count',
        /** Số lần xem bài học và video giáo dục trong kỳ */
        'lesson_video_views',
        /** Điểm trung bình các chỉ số của con (thang điểm 0-100) */
        'child_score_avg',
        /** Điểm chuẩn hóa tiêu chí thời gian sử dụng (0-100) */
        'score_usage',
        /** Điểm chuẩn hóa tiêu chí tần suất sử dụng (0-100) */
        'score_frequency',
        /** Điểm chuẩn hóa tiêu chí số bài đánh giá (0-100) */
        'score_assessment',
        /** Điểm chuẩn hóa tiêu chí xem bài học & video (0-100) */
        'score_lesson_video',
        /** Điểm chuẩn hóa tiêu chí chỉ số của con (0-100) */
        'score_child',
        /** Tổng điểm phân hạng trong kỳ (thang điểm 0-100) */
        'total_points',
        /** Cấp bậc phụ huynh đạt được (0: D, 1: C, 2: B, 3: A) */
        'rank',
        /** Thứ hạng xếp hạng trong kỳ (Leaderboard) */
        'position',
        /** Đánh dấu tháng đã chốt sổ chính thức (không tính lại) */
        'is_final',
        /** Bản chụp cấu hình trọng số và mục tiêu tại thời điểm tính toán */
        'config_snapshot',
        /** Thời điểm tính toán */
        'calculated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'usage_minutes' => 'integer',
        'active_days' => 'integer',
        'session_count' => 'integer',
        'assessment_count' => 'integer',
        'lesson_video_views' => 'integer',
        'child_score_avg' => 'float',
        'score_usage' => 'float',
        'score_frequency' => 'float',
        'score_assessment' => 'float',
        'score_lesson_video' => 'float',
        'score_child' => 'float',
        'total_points' => 'float',
        'rank' => ParentRank::class,
        'position' => 'integer',
        'is_final' => 'boolean',
        'config_snapshot' => 'array',
        'calculated_at' => 'datetime',
    ];

    /**
     * Người dùng (phụ huynh)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
