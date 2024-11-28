<?php

namespace App\Models;

use App\Enums\Notification\MessageType;
use App\Enums\Notification\NotificationStatus;
use App\Enums\VerifiedStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        /** user_id */
        'user_id',
        /** admin_id */
        'admin_id',
        /** Tiêu đề thông báo */
        'title',
        /** Nội dung thông báo */
        'message',
        /** Trạng thái thông báo 1: Chưa đọc, 2: Đã đọc */
        'status',
        /** Thời gian đọc */
        'read_at',
    ];

    protected $casts = [
        'status' => NotificationStatus::class,
        'type' => MessageType::class,
        'is_verified' => VerifiedStatus::class
    ];


    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    // Cập nhật trạng thái của thông báo
    public function markAsRead(): void
    {
        $this->status = NotificationStatus::READ;
        $this->read_at = now();
        $this->save();
    }

    public function scopeUnread($query)
    {
        return $query->where('status', NotificationStatus::NOT_READ);
    }
}
