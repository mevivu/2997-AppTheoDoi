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
        /** code */
        'code',
        /** Google Order Id */
        'google_order_id',
        /** Mã token của giao dịch Google Play */
        'purchase_token',
        /** ID người dùng thực hiện giao dịch */
        'user_id',
        /** ID gói dịch vụ liên quan đến giao dịch */
        'package_id',
        /** Số tiền của giao dịch */
        'amount',
        /** Loại giao dịch (thanh toán, hoàn tiền, ...) */
        'type',
        /** Trạng thái xóa của giao dịch */
        'is_deleted',
        /** Trạng thái giao dịch (chờ xử lý, đã xác nhận, thành công) */
        'status',
        /** Loai */
        'service'
    ];

    protected $casts = [
        'type' => TransactionType::class,
        'is_deleted' => DeleteStatus::class,
        'status' => TransactionStatus::class,
        'service' => TransactionEnumService::class,
    ];

    public function userPackages(): HasMany
    {
        return $this->hasMany(UserPackage::class);
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

}
