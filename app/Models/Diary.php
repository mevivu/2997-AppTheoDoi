<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Nhật ký toa thuốc */
class Diary extends Model
{
    use HasFactory;

    protected $table = 'Diaries';

    protected $fillable = [
        /** Tiêu đề */
        'title',
        /** Nội dung */
        'content',
        /** Hình ảnh */
        'image',
        /** User ID */
        'user_id'

    ];

    protected $casts = [

    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
