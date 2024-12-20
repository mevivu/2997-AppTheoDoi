<?php

namespace App\Models;

use App\Enums\Question\QuestionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Đánh giá*/
class Rating extends Model
{
    use HasFactory;

    protected $table = 'ratings';

    protected $fillable = [
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
    ];
    protected $casts = [
        'type' => QuestionType::class,
    ];


}
