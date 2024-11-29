<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\User\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Bài tập thể chất  */
class Exercise extends Model
{
    use HasFactory;

    protected $table = 'exercises';

    protected $fillable = [
        /** Tên */
        'name',
        /** Mô tả */
        'description',
        /** Trạng thái */
        'status',
    ];
    protected $casts = [
        'birthday' => 'date',
        'gender' => Gender::class,
        'status' => ActiveStatus::class,
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
