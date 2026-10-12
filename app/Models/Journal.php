<?php

namespace App\Models;

use App\Enums\Journal\JournalType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Journal extends Model
{
    use HasFactory;

    protected $table = 'journals';

    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        /** Tiêu đề nhật ký */
        'title',
        /** Nội dung chi tiết của nhật ký */
        'content',
        /** Đường dẫn hình ảnh liên quan */
        'image',
        /** ID trẻ em */
        'child_id',
        /** ID cột mốc gợi ý (nếu có) */
        'milestone_id',
        /** Loại nhật ký */
        'type',
        /** Ngày diễn ra khoảnh khắc */
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => JournalType::class,
        'milestone_id' => 'integer',
    ];

    /**
     * Bé sở hữu nhật ký
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * Cột mốc gợi ý gắn với nhật ký
     */
    public function milestone(): BelongsTo
    {
        return $this->belongsTo(JournalMilestone::class, 'milestone_id');
    }

    /**
     * Phụ huynh sở hữu nhật ký (thông qua bé)
     */
    public function user(): HasOneThrough
    {
        return $this->hasOneThrough(User::class, Child::class, 'id', 'id', 'child_id', 'user_id');
    }
}
