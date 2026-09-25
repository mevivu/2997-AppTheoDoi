<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Chủ đề thẻ bài trong trò chơi Memo Game */
class MemoTheme extends Model
{
    use HasFactory;

    protected $table = 'memo_themes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        /** Tên chủ đề (Phương tiện, Hoa quả, Động vật...) */
        'name',
        /** Mã định danh chủ đề (vehicles, flowers, numbers...) */
        'code',
        /** Độ tuổi áp dụng chủ đề (ví dụ: 1, 2, 3...) */
        'age',
        /** Đường dẫn ảnh đại diện hoặc icon chủ đề */
        'icon',
        /** Đường dẫn hình ảnh mặt sau thẻ bài (mặt úp) */
        'card_back',
        /** Loại ảnh mặt úp ban đầu trên app: 'theme' (ảnh chủ đề), 'logo' (ảnh logo) */
        'card_back_type',
        /** Mô tả chi tiết về chủ đề */
        'description',
        /** Thứ tự hiển thị / sắp xếp */
        'position',
        /** Trạng thái hoạt động (active, draft, deleted) */
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => ActiveStatus::class,
        'position' => 'integer',
        'age' => 'integer',
    ];

    /**
     * Scope lọc chủ đề theo độ tuổi
     */
    public function scopeForAge($query, int $age)
    {
        return $query->where('age', $age);
    }

    /**
     * Danh sách tất cả thẻ bài thuộc chủ đề này
     */
    public function cards(): HasMany
    {
        return $this->hasMany(MemoCard::class, 'memo_theme_id', 'id')->orderBy('position', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Danh sách thẻ bài đang hoạt động thuộc chủ đề này
     */
    public function activeCards(): HasMany
    {
        return $this->hasMany(MemoCard::class, 'memo_theme_id', 'id')
            ->where('status', ActiveStatus::Active->value)
            ->orderBy('position', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * Lịch sử các bài đánh giá sử dụng chủ đề này
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(MemoRating::class, 'memo_theme_id', 'id');
    }

    public function competitions()
    {
        return $this->belongsToMany(MemoCompetition::class, 'memo_competition_themes', 'memo_theme_id', 'memo_competition_id');
    }
}
