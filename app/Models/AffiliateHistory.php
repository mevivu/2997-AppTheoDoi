<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Lịch Sử Nhận Thưởng / Biến Động Số Dư Hoa Hồng Affiliate
 *
 * Đại diện cho bảng 'affiliate_histories', lưu trữ chi tiết từng lần
 * cộng tiền thưởng hoa hồng giới thiệu, chào mừng hoặc các biến động ví affiliate của người dùng.
 */
class AffiliateHistory extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong cơ sở dữ liệu
     *
     * @var string
     */
    protected $table = 'affiliate_histories';

    /**
     * Danh sách các trường được phép gán hàng loạt (Mass Assignment)
     *
     * @var array<int, string>
     */
    protected $fillable = [
        /** ID người dùng nhận thưởng / sở hữu ví */
        'user_id',
        /** ID người dùng mới tạo ra hoa hồng (F1 đăng ký) */
        'source_user_id',
        /** Số tiền hoa hồng biến động (VNĐ) */
        'amount',
        /** Số dư ví hoa hồng sau khi được cộng tiền (VNĐ) */
        'balance_after',
        /** Loại thưởng (ví dụ: referral_register - thưởng giới thiệu F1, welcome_register - thưởng chào mừng F1) */
        'type',
        /** Diễn giải chi tiết lý do cộng tiền */
        'description',
    ];

    /**
     * Khai báo kiểu dữ liệu cần ép kiểu (Casting)
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:0',
        'balance_after' => 'decimal:0',
    ];

    /**
     * Liên kết lấy thông tin tài khoản người dùng nhận thưởng hoa hồng
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Liên kết lấy thông tin thành viên mới đã kích hoạt hoa hồng (người nhập mã giới thiệu)
     *
     * @return BelongsTo
     */
    public function sourceUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'source_user_id');
    }
}
