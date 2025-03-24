<?php

namespace App\Models;

use App\Enums\Question\QuestionType;
use App\Enums\VerifiedStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Đánh giá Tổng Quan*/
class Rating extends Model
{
    use HasFactory;

    protected $table = 'ratings';

    protected $fillable = [
        /** ID của bé */
        'child_id',
        /** Điểm đánh giá */
        'score',
        /** Mô tả chi tiết về đánh giá */
        'description',
        /** Thẻ gắn, có thể dùng để phân loại thêm */
        'tag',
        /** Kết quả đánh giá */
        'result',
        /** Loại câu hỏi hoặc đánh giá */
        'type',
        /** Kiểm soát cảm xúc */
        'self_regulation',
        /** Nhận thức cảm xúc */
        'social_awareness',
        /** Đồng cảm */
        'relationship_management',
        /** Động lực */
        'decision_making',
        /** Kỹ năng xã hội */
        'optimism',
        /** Hình ảnh danh hiệu */
        'badge_image',
        /** Tuôi */
        'age',
        /** Trạng thái */
        'status',
        /** Nhãn */
        'label',
        /** Khả năng chịu đựng */
        'endurance',
        /** Tính linh hoạt */
        'flexibility',
        /** Tính kiên trì */
        'perseverance',
        /** Tính tích cực */
        'positivity',
        /** Khả năng tự phản hồi */
        'self_reflection'
    ];
    protected $casts = [
        'type' => QuestionType::class,
        'status' => VerifiedStatus::class,
    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id');
    }


}
