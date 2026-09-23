<?php

namespace App\Models;

use App\Enums\Package\PackageDiscountType;
use App\Enums\Package\PackageStatus;
use App\Enums\Package\PackageType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Gói dịch vụ */
class Package extends Model
{
    use HasFactory;

    protected $table = 'packages';

    protected $fillable = [
        /* Tên gói dịch vụ */
        'name',
        /* Giá gói dịch vụ */
        'price',
        /** Số ngày */
        'days',
        /* Mô tả gói dịch vụ */
        'description',
        /* Trạng thái của gói dịch vụ */
        'status',
        /* Loại gói dịch vụ (1 tháng, 3 tháng, 6 tháng, 1 năm) */
        'type',
        'code',
        /* Loại giảm giá (none, percent, fixed) */
        'discount_type',
        /* Giá trị giảm (% hoặc số tiền) */
        'discount_value',
        /* Mã giảm giá / Voucher */
        'discount_code',
        /* Số thiết bị đăng nhập tối đa */
        'max_devices',
        /* Gói tự động gia hạn (true) hoặc một lần / không gia hạn (false) */
        'is_auto_renew',
        /* Phân loại: Gói sale (true) hoặc Gói thường (false) */
        'is_sale',
        /* Thời gian bắt đầu sale */
        'sale_start_at',
        /* Thời gian kết thúc sale */
        'sale_end_at',
    ];
    protected $casts = [
        'price' => 'float',
        'status' => PackageStatus::class,
        'type' => PackageType::class,
        'discount_type' => PackageDiscountType::class,
        'discount_value' => 'float',
        'max_devices' => 'integer',
        'is_auto_renew' => 'boolean',
        'is_sale' => 'boolean',
        'sale_start_at' => 'datetime',
        'sale_end_at' => 'datetime',
    ];

    protected $appends = [
        'final_price',
        'discount_amount',
        'has_discount',
        'discount_display',
        'is_sale_active',
        'sale_start_time',
        'ad_duration_hours',
    ];

    /**
     * Số tiền thực tế được giảm (VNĐ)
     */
    public function getDiscountAmountAttribute(): float
    {
        $price = (float) $this->price;
        $finalPrice = (float) $this->final_price;
        return max(0, round($price - $finalPrice));
    }

    /**
     * Tính giá bán sau khi áp dụng giảm giá
     */
    public function getFinalPriceAttribute(): float
    {
        $price = (float) $this->price;
        $discountValue = (float) $this->discount_value;

        if ($this->discount_type === PackageDiscountType::Percent && $discountValue > 0) {
            $discountAmount = $price * ($discountValue / 100);
            return max(0, round($price - $discountAmount));
        }

        if ($this->discount_type === PackageDiscountType::Fixed && $discountValue > 0) {
            return max(0, round($price - $discountValue));
        }

        return $price;
    }

    /**
     * Kiểm tra gói có đang được giảm giá không
     */
    public function getHasDiscountAttribute(): bool
    {
        return $this->discount_type !== null
            && $this->discount_type !== PackageDiscountType::None
            && (float) $this->discount_value > 0;
    }

    /**
     * Chuỗi hiển thị mức giảm giá trực quan (ví dụ: -20% hoặc -50.000 đ)
     */
    public function getDiscountDisplayAttribute(): string
    {
        if (!$this->has_discount) {
            return '';
        }

        if ($this->discount_type === PackageDiscountType::Percent) {
            return '-' . (int) $this->discount_value . '%';
        }

        if ($this->discount_type === PackageDiscountType::Fixed) {
            return '-' . number_format($this->discount_value, 0, ',', '.') . ' đ';
        }

        return '';
    }

    /**
     * Kiểm tra đợt sale có đang hiệu lực không
     */
    public function getIsSaleActiveAttribute(): bool
    {
        if (!$this->is_sale) {
            return false;
        }

        $now = now();

        if ($this->sale_start_at && $now->lt($this->sale_start_at)) {
            return false;
        }

        if ($this->sale_end_at && $now->gt($this->sale_end_at)) {
            return false;
        }

        return true;
    }

    /**
     * Trạng thái sale: none, upcoming, active, expired
     */
    public function getSaleStatusAttribute(): string
    {
        if (!$this->is_sale) {
            return 'none';
        }

        $now = now();

        if ($this->sale_start_at && $now->lt($this->sale_start_at)) {
            return 'upcoming';
        }

        if ($this->sale_end_at && $now->gt($this->sale_end_at)) {
            return 'expired';
        }

        return 'active';
    }

    /**
     * Chuỗi hiển thị khoảng thời gian sale trực quan
     */
    public function getSalePeriodDisplayAttribute(): string
    {
        if (!$this->is_sale) {
            return '';
        }

        if ($this->sale_start_at && $this->sale_end_at) {
            return $this->sale_start_at->format('d/m/Y H:i') . ' - ' . $this->sale_end_at->format('d/m/Y H:i');
        }

        if ($this->sale_start_at) {
            return 'Từ ' . $this->sale_start_at->format('d/m/Y H:i');
        }

        if ($this->sale_end_at) {
            return 'Đến ' . $this->sale_end_at->format('d/m/Y H:i');
        }

        return 'Không giới hạn';
    }

    /**
     * Thời gian bắt đầu sale theo chuẩn ISO (sale_start_time)
     */
    public function getSaleStartTimeAttribute(): ?string
    {
        if (!$this->is_sale || !$this->sale_start_at) {
            return null;
        }

        return $this->sale_start_at->format('Y-m-d\TH:i:s.u');
    }

    /**
     * Thời lượng sale tính theo giờ (ad_duration_hours)
     * Ví dụ: 6 tiếng
     */
    public function getAdDurationHoursAttribute(): int|float|null
    {
        if (!$this->is_sale || !$this->sale_start_at || !$this->sale_end_at) {
            return null;
        }

        $diffSeconds = max(0, $this->sale_start_at->diffInSeconds($this->sale_end_at, false));
        $hours = $diffSeconds / 3600;

        return ($diffSeconds % 3600 === 0) ? (int) $hours : round($hours, 2);
    }

    public function userPackages(): HasMany
    {
        return $this->hasMany(UserPackage::class);
    }


    public static function getTrialPackage(): ?self
    {
        return self::where('type', PackageType::Trial)->first();
    }

    public static function getNormalPackage(): ?self
    {
        return self::where('type', PackageType::Normal)->first();
    }


}
