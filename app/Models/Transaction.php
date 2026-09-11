<?php

namespace App\Models;

use App\Enums\DeleteStatus;
use App\Enums\Transaction\TransactionEnumService;
use App\Enums\Transaction\TransactionStatus;
use App\Enums\Transaction\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Giao dịch  */
class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        /** Mã định danh giao dịch duy nhất (VD: PAY..., WDR...) */
        'code',
        /** Mã Order ID từ Store (Dùng chung lưu Transaction ID của cả Google Play và Apple Store) */
        'google_order_id',
        /** Mã purchase token của giao dịch từ Google Play / Apple Store */
        'purchase_token',
        /** ID người dùng / đối tác thực hiện giao dịch */
        'user_id',
        /** ID gói dịch vụ liên quan (null đối với giao dịch rút tiền) */
        'package_id',
        /** Số tiền của giao dịch (VNĐ) */
        'amount',
        /** Loại giao dịch (payment: thanh toán mua gói, withdraw: rút tiền hoa hồng, ...) */
        'type',
        /** Trạng thái xóa mềm của giao dịch (deleted / not_deleted) */
        'is_deleted',
        /** Trạng thái giao dịch (pending: chờ xử lý, confirmed: đã duyệt, success: thành công, refunded: hoàn tiền/từ chối) */
        'status',
        /** Nguồn cổng thanh toán / dịch vụ (GOOGLE_PLAY, APP_STORE, NORMAL,...) */
        'service',
        /** Tên ngân hàng nhận tiền chi trả (VD: Vietcombank, MB Bank,...) */
        'bank_name',
        /** Số tài khoản ngân hàng của người nhận */
        'bank_account_number',
        /** Tên chủ tài khoản ngân hàng */
        'bank_account_name',
        /** Ngày Thứ 5 dự kiến chi trả tiền hoa hồng */
        'scheduled_payout_date',
        /** Ghi chú của admin, mã biên lai giao dịch ngân hàng hoặc lý do từ chối */
        'admin_note',
        /** ID quản trị viên (Admin) duyệt hoặc từ chối lệnh */
        'processed_by',
        /** Thời điểm quản trị viên xử lý lệnh */
        'processed_at',
    ];

    protected $casts = [
        'type' => TransactionType::class,
        'is_deleted' => DeleteStatus::class,
        'status' => TransactionStatus::class,
        'service' => TransactionEnumService::class,
        'scheduled_payout_date' => 'date',
        'processed_at' => 'datetime',
        'amount' => 'decimal:0',
    ];

    /**
     * Danh sách các gói người dùng kích hoạt từ giao dịch
     */
    public function userPackages(): HasMany
    {
        return $this->hasMany(UserPackage::class);
    }

    /**
     * Người dùng / đối tác thực hiện giao dịch
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Gói dịch vụ được mua trong giao dịch (nếu là giao dịch mua gói)
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Quản trị viên (Admin) đã duyệt hoặc từ chối lệnh rút tiền
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'processed_by');
    }
}
