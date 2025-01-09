<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Bước của hướng dẫn */
class Step extends Model
{
    use HasFactory;


    protected $table = 'steps';


    protected $fillable = [
        /* ID của hướng dẫn liên quan */
        'guide_id',
        /* Tiêu đề của bước */
        'title',
        /* Mô tả của bước */
        'description',
        /* Thứ tự của bước */
        'order',
    ];

    public function guide(): BelongsTo
    {
        return $this->belongsTo(Guide::class, 'guide_id');
    }
}
