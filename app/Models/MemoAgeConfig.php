<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\Memo\MemoConfigType;
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
        /** Loại cấu hình: iq_test hoặc competition */
        'type',
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
        /** Số lượt mở tối đa (cả đúng và sai) trước khi Game Over (0 = không giới hạn) */
        'max_moves',
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
        'max_moves' => 'integer',
        'type' => MemoConfigType::class,
        'status' => ActiveStatus::class,
    ];

    /**
     * Lịch sử các bài đánh giá áp dụng cấu hình này
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(MemoRating::class, 'memo_age_config_id', 'id');
    }

    public function personalBests(): HasMany
    {
        return $this->hasMany(MemoPersonalBest::class, 'memo_age_config_id', 'id');
    }

    public function competitions(): HasMany
    {
        return $this->hasMany(MemoCompetition::class, 'memo_age_config_id', 'id');
    }

    /**
     * Scope lọc cấu hình dành cho Bài kiểm tra IQ
     */
    public function scopeIqTest($query)
    {
        return $query->where('type', MemoConfigType::IqTest->value);
    }

    /**
     * Scope lọc cấu hình dành cho Giải đấu
     */
    public function scopeCompetition($query)
    {
        return $query->where('type', MemoConfigType::Competition->value);
    }

    /**
     * Scope tìm kiếm cấu hình phù hợp với độ tuổi của bé (mặc định lấy loại Bài kiểm tra IQ)
     */
    public function scopeForAge($query, int $age, ?MemoConfigType $type = MemoConfigType::IqTest)
    {
        return $query->where('status', ActiveStatus::Active->value)
            ->when($type, fn ($q) => $q->where('type', $type->value))
            ->where('min_age', '<=', $age)
            ->where('max_age', '>=', $age);
    }
}
