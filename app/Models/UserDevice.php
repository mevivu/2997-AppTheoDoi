<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Mô hình UserDevice
 *
 * Quản lý các thiết bị đăng nhập liên kết với tài khoản người dùng theo chính sách giới hạn thiết bị của gói cước.
 */
class UserDevice extends Model
{
    use HasFactory;

    protected $table = 'user_devices';

    protected $fillable = [
        /** ID người dùng */
        'user_id',
        /** Định danh thiết bị duy nhất (UUID/Hardware ID hoặc Token) */
        'device_id',
        /** Tên dòng máy (iPhone 15, Samsung S24...) */
        'device_name',
        /** Token thiết bị dùng nhận thông báo Firebase (FCM) */
        'device_token',
        /** Địa chỉ IP đăng nhập */
        'ip_address',
        /** Trạng thái liên kết: true = đang hoạt động, false = đã giải phóng */
        'is_active',
        /** Thời điểm hoạt động gần nhất */
        'last_active_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_active_at' => 'datetime',
    ];

    /**
     * Người dùng sở hữu thiết bị này
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
