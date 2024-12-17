<?php

namespace App\Models;

use App\Enums\Child\BornStatus;
use App\Enums\User\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\Child\ChildStatus;

class Child extends Model
{
    use HasFactory;

    protected $table = 'children';

    protected $fillable = [
        /** Họ tên */
        'fullname',
        /** Ngày sinh */
        'birthday',
        /** Giới tính */
        'gender',
        /** Hình ảnh */
        'avatar',
        /** User ID */
        'user_id',
        /** Trạng thái */
        'status',
        /** Trạng thái sinh */
        'is_born'
    ];
    protected $casts = [
        'birthday' => 'date',
        'gender' => Gender::class,
        'status' => ChildStatus::class,
        'is_born' => BornStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
