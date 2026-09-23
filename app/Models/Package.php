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
        'sale_remaining_seconds',
        'sale_remaining_time',
        'sale_countdown',
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
     * Số giây còn lại của đợt sale (tính từ now đến sale_end_at)
     * Trả về int số giây, hoặc 0 nếu đã hết hạn, null nếu không phải gói sale hoặc không giới hạn.
     */
    public function getSaleRemainingSecondsAttribute(): ?int
    {
        if (!$this->is_sale) {
            return null;
        }

        if (!$this->sale_end_at) {
            return null; // Không giới hạn
        }

        $now = now();
        if ($now->gte($this->sale_end_at)) {
            return 0;
        }

        return max(0, (int) $now->diffInSeconds($this->sale_end_at, false));
    }

    /**
     * Chuỗi thời gian còn lại của đợt sale trực quan
     * Dưới 24h: "05:59:48" (Giờ:Phút:Giây)
     * Từ 24h trở lên: "17 ngày 01:01:00"
     */
    public function getSaleRemainingTimeAttribute(): ?string
    {
        if (!$this->is_sale) {
            return null;
        }

        if (!$this->sale_end_at) {
            return 'Không giới hạn';
        }

        $now = now();
        if ($now->gte($this->sale_end_at)) {
            return '00:00:00';
        }

        if ($this->sale_start_at && $now->lt($this->sale_start_at)) {
            return 'Chưa bắt đầu';
        }

        $totalSeconds = max(0, (int) $now->diffInSeconds($this->sale_end_at, false));
        $days = intdiv($totalSeconds, 86400);
        $hours = intdiv($totalSeconds % 86400, 3600);
        $minutes = intdiv($totalSeconds % 3600, 60);
        $seconds = $totalSeconds % 60;

        if ($days > 0) {
            return sprintf('%d ngày %02d:%02d:%02d', $days, $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    /**
     * Dữ liệu countdown chi tiết cho giao diện đồng hồ đếm ngược (Giờ, Phút, Giây)
     */
    public function getSaleCountdownAttribute(): ?array
    {
        if (!$this->is_sale) {
            return null;
        }

        if (!$this->sale_end_at) {
            return null;
        }

        $now = now();
        $isExpired = $now->gte($this->sale_end_at);
        $totalSeconds = $isExpired ? 0 : max(0, (int) $now->diffInSeconds($this->sale_end_at, false));

        $days = intdiv($totalSeconds, 86400);
        $hours = intdiv($totalSeconds % 86400, 3600);
        $totalHours = intdiv($totalSeconds, 3600);
        $minutes = intdiv($totalSeconds % 3600, 60);
        $seconds = $totalSeconds % 60;

        return [
            'days' => $days,
            'hours' => $hours,
            'total_hours' => $totalHours,
            'minutes' => $minutes,
            'seconds' => $seconds,
            'total_seconds' => $totalSeconds,
            'formatted' => $days > 0 
                ? sprintf('%d ngày %02d:%02d:%02d', $days, $hours, $minutes, $seconds)
                : sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds),
            'formatted_hms' => sprintf('%02d:%02d:%02d', $totalHours, $minutes, $seconds),
        ];
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
