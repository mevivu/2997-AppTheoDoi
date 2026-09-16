<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Cấu hình kích thước lưới thẻ và thời gian theo độ tuổi của bé */
class MemoAgeConfig extends Model
{
    use HasFactory;

    protected $table = 'memo_age_configs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        /** Tên mức độ cấu hình (Khởi động, Cơ bản, Nâng cao, Thử thách...) */
        'name',
        /** Độ tuổi tối thiểu áp dụng */
        'min_age',
        /** Độ tuổi tối đa áp dụng */
        'max_age',
        /** Số hàng của lưới thẻ */
        'rows',
        /** Số cột của lưới thẻ */
        'columns',
        /** Tổng số thẻ bài trong lưới (rows * columns) */
        'total_cards',
        /** Số cặp thẻ cần ghép đúng (total_cards / 2) */
        'pairs_count',
        /** Tổng thời gian làm bài kiểm tra (giây) */
        'total_duration',
        /** Số lượt game trong bài kiểm tra (mặc định 3 lượt) */
        'total_rounds',
        /** Thời gian cho bé ghi nhớ trước khi úp thẻ (giây) */
        'peek_time',
        /** Trạng thái cấu hình (active, draft, deleted) */
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'min_age' => 'integer',
        'max_age' => 'integer',
        'rows' => 'integer',
        'columns' => 'integer',
        'total_cards' => 'integer',
        'pairs_count' => 'integer',
        'total_duration' => 'integer',
        'total_rounds' => 'integer',
        'peek_time' => 'integer',
        'status' => ActiveStatus::class,
    ];

    /**
     * Lịch sử các bài đánh giá áp dụng cấu hình này
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(MemoRating::class, 'memo_age_config_id', 'id');
    }

    /**
     * Scope tìm kiếm cấu hình phù hợp với độ tuổi của bé
     */
    public function scopeForAge($query, int $age)
    {
        return $query->where('status', ActiveStatus::Active->value)
            ->where('min_age', '<=', $age)
            ->where('max_age', '>=', $age);
    }
}
