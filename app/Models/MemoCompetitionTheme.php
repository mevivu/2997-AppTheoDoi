<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Cấu hình thứ tự chủ đề ván thi trong giải đấu Memo Game
 * Xác định thứ tự 4 ván thi liên tiếp (1, 2, 3, 4) và chủ đề tương ứng
 */
class MemoCompetitionTheme extends Model
{
    use HasFactory;

    protected $table = 'memo_competition_themes';

    /**
     * Danh sách các trường được phép gán dữ liệu hàng loạt
     */
    protected $fillable = [
        /** ID giải đấu Memo Game */
        'memo_competition_id',
        /** ID chủ đề thẻ bài tương ứng */
        'memo_theme_id',
        /** Thứ tự ván thi liên tiếp trong lượt thi (1 -> 4) */
        'game_order',
    ];

    /**
     * Ép kiểu dữ liệu
     */
    protected $casts = [
        'memo_competition_id' => 'integer',
        'memo_theme_id' => 'integer',
        'game_order' => 'integer',
    ];

    /**
     * Giải đấu chứa cấu hình chủ đề này
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(MemoCompetition::class, 'memo_competition_id', 'id');
    }

    /**
     * Thông tin chi tiết chủ đề thẻ bài
     */
    public function theme(): BelongsTo
    {
        return $this->belongsTo(MemoTheme::class, 'memo_theme_id', 'id');
    }
}
