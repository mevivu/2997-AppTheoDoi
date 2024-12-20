<?php

namespace App\Models;

use App\Enums\Package\PackageType;
use App\Enums\Package\PackageUserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Mô hình UserPackage
 *
 * Đây là bảng liên kết giữa người dùng và các gói dịch vụ.
 */

class UserPackage extends Model
{
    use HasFactory;

    protected $table = 'user_packages';

    protected $fillable = [
        'user_id',
        'package_id',
        /* Ngày bắt đầu gói dịch vụ */
        'start_date',
        /* Ngày kết thúc gói dịch vụ */
        'end_date',
        /* Trạng thái của gói dịch vụ */
        'status',
        /* Loại gói dịch vụ (1 tháng, 3 tháng, 6 tháng, 1 năm) */
        'current_type',
    ];
    protected $casts = [

        'status' => PackageUserStatus::class,
        'current_type' => PackageType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

}
