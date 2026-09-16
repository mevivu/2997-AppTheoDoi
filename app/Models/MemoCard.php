<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Thẻ bài ghi nhớ trong Memo Game */
class MemoCard extends Model
{
    use HasFactory;

    protected $table = 'memo_cards';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        /** ID chủ đề thẻ bài thuộc về */
        'memo_theme_id',
        /** Tên thẻ bài (Ô tô, Xe buýt, Quả táo...) */
        'name',
        /** Đường dẫn hình ảnh mặt trước thẻ bài */
        'image',
        /** Đường dẫn tệp âm thanh phát âm khi lật đúng cặp thẻ */
        'audio',
        /** Thứ tự hiển thị / sắp xếp thẻ bài */
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
        'memo_theme_id' => 'integer',
        'position' => 'integer',
        'status' => ActiveStatus::class,
    ];

    /**
     * Chủ đề mà thẻ bài này trực thuộc
     */
    public function theme(): BelongsTo
    {
        return $this->belongsTo(MemoTheme::class, 'memo_theme_id', 'id');
    }
}
