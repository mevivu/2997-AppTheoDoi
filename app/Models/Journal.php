<?php

namespace App\Models;

use App\Enums\Journal\JournalType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Journal extends Model
{
    use HasFactory;

    protected $table = 'journals';

    protected $fillable = [
        /** Tiêu đề nhật ký */
        'title',
        /** Nội dung chi tiết của nhật ký */
        'content',
        /** Đường dẫn hình ảnh liên quan */
        'image',
        /** ID người dùng liên quan */
        'user_id',
        /** Loại */
        'type'
    ];
    protected $casts = [
        'type' => JournalType::class
    ];


    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    public function user(): BelongsTo
    {
        return $this->child()->user();
    }
}
