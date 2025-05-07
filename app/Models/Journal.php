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

    public $timestamps = true;


    protected $fillable = [
        /** Tiêu đề nhật ký */
        'title',
        /** Nội dung chi tiết của nhật ký */
        'content',
        /** Đường dẫn hình ảnh liên quan */
        'image',
        /** ID trẻ em */
        'child_id',
        /** Loại */
        'type',
        'created_at',

    ];

    protected $casts = [
        'type' => JournalType::class,

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
